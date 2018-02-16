#!/usr/bin/env bash
set -e

read -p "Enter user with domain, e.g. user@example.com: " USERNAME

# Remove exisiting archive
rm archive.zip


# Zip directory
 zip -x ".git/*" -x ".vscode/*" -x "config/*" -r archive.zip ./

 # Upload zip to scouting
scp archive.zip ${USERNAME}:/home/wampa3506/scouting

# Unzip file archive
ssh -tt ${USERNAME} << EOF
cd /home/wampa3506/scouting
unzip -o archive.zip
rm archive.zip