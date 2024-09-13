#!/bin/bash

#Generated file, part of preupgrade-assistant content, should not be used
#separately, see preupgrade-assistant license for licensing details
#Do the upgrade for the packages with potentially broken obsoletes/provides

prep_source_right() {
  # return 0 - mounted successfully
  # return 1 - nothing to do
  # return 2 - mount failed

  RHELUP_CONF="/root/preupgrade/upgrade.conf"
  mount_path="$(grep "^device" "$RHELUP_CONF" | sed -r "s/^.*rawmnt='([^']+)', .*$/\1/")"
  iso_path="$(grep "^iso" "$RHELUP_CONF" | cut -d " " -f 3- | grep -vE "^None$")"
  device_line="$(grep "^device" "$RHELUP_CONF"  | cut -d " " -f 3- | grep -vE "^None$")"
  device_path="$(echo "$device_line"  | sed -r "s/^.*dev='([^']+)',.*/\1/")"
  fs_type="$(echo "$device_line" | grep -o "type='[^']*'," | sed -r "s/^type='(.*)',$/\1/" )"
  m_opts="$(echo "$device_line" | grep -o "opts='[^']*'," | sed -r "s/^opts='(.*)',$/\1/" )"

  # is used iso or device? if not, return 1
  [ -n "$mount_path" ] && { [ -n "$iso_path" ] || [ -n "$device_path" ]; } || return 1
  mountpoint -q "$mount_path" && return 1 # is already mounted
  if [ -n "$iso_path" ]; then
    mount -t iso9660 -o loop,ro "$iso_path"  "$mount_path" || return 2
  else
    # device
    [ -n "$fs_type" ] && fs_type="-t $fs_type"
    [ -n "$m_opts" ] && m_opts="-o $m_opts"
    mount $fs_type $m_opts "$device_path" "$mount_path" || return 2
  fi

  return 0
}


for i in $(echo " bfa-firmware|linux-firmware coreutils-libs|coreutils db4|libdb db4-utils|libdb-utils libudev|systemd-libs nfs-utils-lib|libnfsidmap ql2100-firmware|linux-firmware ql2200-firmware|linux-firmware ql23xx-firmware|linux-firmware ql2400-firmware|linux-firmware ql2500-firmware|linux-firmware rt61pci-firmware|linux-firmware rt73usb-firmware|linux-firmware ruby-rdoc|rubygem-rdoc")
do
  old="$(echo $i | cut -d'|' -f1)"
  new="$(echo $i | cut -d'|' -f2 | tr ',' ' ')"
  #we want to remove the old package if still present
  rpm -q $old 2>/dev/null >/dev/null && {
  #Store the modified files as .preupsave
  for j in $(rpm -V $old | rev | cut -d' ' -f1 | rev | grep -v "(replaced)")
  do
    cp $j $j.preupsave
    echo "Storing a modified $j file from the $old package as $j.preupsave"
  done
  #deinstall the old package
  rpm -e $old --nodeps
  echo "The $old package was uninstalled."
  }
  #do we already have all new installed? Skip it...
  rpm -q $new >/dev/null && continue
  yum install -y $new || {
    prep_source_right && \
      yum install -y $new
  }
  rpm -q $new 2>/dev/null >/dev/null && echo "The $new package or packages installed" && continue
  #when we are here, installation got wrong and we should warn the user.
  echo  "The automatic installation of the $new package or packages failed, install the package or packages manually."
done
for old in $(echo " bfa-firmware coreutils-libs db4 db4-utils dracut-kernel iptables-ipv6 jpackage-utils kernel-firmware libudev man module-init-tools nfs-utils-lib perl-Compress-Zlib perl-IO-Compress-Base perl-IO-Compress-Zlib procps python-argparse ql2100-firmware ql2200-firmware ql23xx-firmware ql2400-firmware ql2500-firmware rt61pci-firmware rt73usb-firmware ruby-rdoc util-linux-ng xorg-x11-drv-ati-firmware yum-plugin-security")
do
  #we want to remove the old package if still present
  rpm -q $old 2>/dev/null >/dev/null && {
  #Store the modified files as .preupsave
  for j in $(rpm -V $old | rev | cut -d' ' -f1 | rev | grep -v "(replaced)")
  do
    cp $j $j.preupsave
    echo "Storing the modified $j file from the $old package as $j.preupsave"
  done
  #deinstall the old package
  rpm -e $old --nodeps
  echo "The $old package was uninstalled."
  }
done
