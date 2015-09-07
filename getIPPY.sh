#!/bin/bash

unamestr=`uname`
if [[ "$unamestr" == 'Linux' ]]; then
    cmp -s ${OPENSHIFT_REPO_DIR}/pyIpBot.py ${OPENSHIFT_REPO_DIR}/ENV/bin/pyIpBot.py > /dev/null
    if [ $? -eq 1 ]; then
        cp ${OPENSHIFT_REPO_DIR}/pyIpBot.py ${OPENSHIFT_REPO_DIR}/ENV/bin/pyIpBot.py
    fi
    cd ${OPENSHIFT_REPO_DIR}/ENV/bin
else
    cmp -s pyIpBot.py ENV/bin/pyIpBot.py > /dev/null
    if [ $? -eq 1 ]; then
        cp pyIpBot.py ENV/bin/pyIpBot.py
    fi
    cd ../ENV/bin
fi

source activate
python -c "import pyIpBot; pyIpBot.getLocation(\"$1\"); "