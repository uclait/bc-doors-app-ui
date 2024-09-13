#!/usr/bin/bash
test -f units_to_enable || exit 0
for unit in $(cat units_to_enable); do
	systemctl enable $unit
done
