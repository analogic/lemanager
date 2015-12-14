#!/usr/bin/with-contenv sh

if [ ! -d "/data" ]; then
    echo ""
    echo -e "\e[41m"
    echo -e " \e[1m[ERROR]\e[21m No volume defined, run docker with \"\e[1m-v /your/certificates/folder:/data\e[21m\" option"
    echo -e "\e[49m"
    echo ""
    exit 1
fi