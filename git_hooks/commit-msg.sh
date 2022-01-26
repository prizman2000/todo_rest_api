#!/bin/bash

commit_regex='#[0-9]+.+'
error_msg='Commit message must match rejex commit regex'

if ! grep -qE "$commit_regex" "$1"; then
	echo "$error_msg"
	exit 1
fi
