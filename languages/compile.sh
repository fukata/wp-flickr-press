#!/usr/bin/env bash

for f in $(find . -name \*.po)
do
    msgfmt -o ${f%.po}.mo $f 
done
