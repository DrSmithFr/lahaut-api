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
MERGE_FOUND=$?

# check for dump tags against all php files
./hooks/src/check-dump.sh ${FILES}
DUMP_FOUND=$?

# check for console.log against JS/TS files
./hooks/src/check-console-log.sh ${FILES}
CONSOLE_LOG_FOUND=$?

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

PHPMD=0
if [[ "$PHPs" != "" ]]
then
    # check php syntax
    display phpmd

    for file in $(echo "$PHPs"); do
        symfony php vendor/bin/phpmd $file ansi phpmd.xml
        SUBMD=$?

        if [[ $PHPMD -eq 0 ]]
        then
            PHPMD=$SUBMD
        fi
    done

    if [[ $PHPMD -eq 0 ]]
    then
        display success "PHP Logic checked"
    else
        display error "PHP Logic errors"
    fi


fi

# Unit Tests running
PHPUNIT=0
symfony php bin/phpunit
PHPUNIT=$?

if [[ $((${MERGE_FOUND} + ${DUMP_FOUND} + ${CONSOLE_LOG_FOUND} + ${PHPCS} + ${PHPMD} + ${PHPUNIT})) -ne 0 ]]
then
    echo "Your code need to be checked"
    exit 1
fi

# Post checkup validation
display final && \
./hooks/src/ask-validation.sh ${FILES}
exit $?
