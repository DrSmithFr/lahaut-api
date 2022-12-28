#!/bin/bash

# Unit Tests running
PHPUNIT=0
symfony php bin/phpunit
PHPUNIT=$?

if [[ ${PHPUNIT} -ne 0 ]]
then
    echo "Your code need to be checked, PHPUnit failed (code ${PHPUNIT})"
    exit 1
fi