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

# Unit Tests running
symfony console doctrine:schema:update --force --env=test
symfony console doctrine:fixtures:load -n --env=test
symfony php bin/phpunit

# Post checkup validation
display final && \
./hooks/src/ask-validation.sh ${FILES}
exit $?
