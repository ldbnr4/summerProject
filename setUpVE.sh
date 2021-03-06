#!/bin/bash

unamestr=`uname`
if [[ "$unamestr" == 'Linux' ]]; then
   virtualenv ${OPENSHIFT_REPO_DIR}/ENV
   cd ${OPENSHIFT_REPO_DIR}/ENV
else
    virtualenv ENV --system-site-packages
    cd ENV
    directory="./Scripts"
    if [ -d $directory ]; then
      mv Scripts bin
    fi
fi

cd ..
cp pyDates.py ENV/bin
cp pyJamBaseBot.py ENV/bin
cp pyPicBot.py ENV/bin
cp update_events.py ENV/bin
cp pyIpBot.py ENV/bin
cd ENV/bin
source activate
unamestr=`uname`
if [[ "$unamestr" == 'Linux' ]]; then
    pip install lxml
    pip install requests
    pip install HTMLParser
    pip install datetime
    pip install python-dateutil
    pip install beautifulsoup4
fi