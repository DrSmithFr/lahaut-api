#!/bin/bash

# loading all hooks helper
source ./hooks/bin/ask.sh
source ./hooks/bin/display.sh
source ./hooks/bin/git.sh

# get all modified files array
FILES=$(git_modified_files $(git_current_commit))

display inspect

# check for merge tags against all files
./hooks/src/check-merge-tags.sh ${FILES}
if [[ $? -ne 0 ]]
then
  echo "Your code need to be checked (Merge tags found)"
  exit 1
fi

# check for dump tags against all php files
./hooks/src/check-dump.sh ${FILES}
if [[ $? -ne 0 ]]
then
  echo "Your code need to be checked (Dump tags found)"
  exit 1
fi

# check for console.log against JS/TS files
./hooks/src/check-console-log.sh ${FILES}
if [[ $? -ne 0 ]]
then
  echo "Your code need to be checked (Console.log found)"
  exit 1
fi

# getting all php files affected by commit
PHPs=$(git_modified_files_by_ext "php" ${FILES})

PHPCS=0
if [[ "$PHPs" != "" ]]
then
    # check php syntax
    display phpcs && \
    symfony php vendor/bin/phpcs --ignore=vendor,bin,public,documentation,migrations ${PHPs} && \
    display success "PSR-2 Syntax checked"
    PHPCS=$?
fi

if [[ ${PHPCS} -ne 0 ]]
then
  echo "Your code need to be checked (PSR-2 Syntax errors)"
  exit 1
fi

PHPMD=0
if [[ "$PHPs" != "" ]]
then
    # check php syntax
    display phpmd

    for file in $(echo "$PHPs"); do
        echo "============================="
        echo "Checking $file:"
        symfony php vendor/bin/phpmd $file ansi phpmd.xml
        SUBMD=$?

        if [[ $PHPMD -eq 0 ]]
        then
            PHPMD=$SUBMD
        fi
    done

    echo "============================="

    if [[ ${PHPMD} -ne 0 ]]
    then
      echo "Your code need to be checked (PHP Mess Detector exited with code ${PHPMD})"
      exit 1
    fi

    display success "PHP Logic checked"
fi

# Unit Tests running
PHPUNIT=0
symfony php bin/phpunit
if [[ $? -ne 0 ]]
then
  echo "Your code need to be checked (PHPUnit exited with code ${PHPUNIT})"
  exit 1
fi

# Post checkup validation
display final && \
./hooks/src/ask-validation.sh ${FILES}
exit $?
