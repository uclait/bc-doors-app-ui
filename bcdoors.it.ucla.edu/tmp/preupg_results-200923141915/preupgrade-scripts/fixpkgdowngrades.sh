#!/bin/bash

if [ "$(id -u)" != 0 ]; then
    echo >&2 "Run this under root."
    exit 1
fi

rootdir="/root/preupgrade/pkgdowngrades/installroot"
rhelupdir=/var/lib/system-upgrade
downloaddir="/root/preupgrade/pkgdowngrades/rpms"

rm -rf "$rootdir"
mkdir -p "$rootdir"
mkdir -p "$downloaddir"

"/root/preupgrade/pkgdowngrades/enforce_downgraded" \
    --destdir="$downloaddir" \
    --installroot="$rootdir" \
    --rhelupdir="$rhelupdir" || exit 1

exit 0
