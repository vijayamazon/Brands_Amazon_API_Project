#!/bin/bash
for i in {1..2}
	do
		echo "hello"
		sleep 5
	done

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
echo $DIR 