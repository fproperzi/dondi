#!/bin/bash
echo -- Leggo dai file .PHP della directory superiore
xgettext -k_e --from-code=UTF-8 --package-name=RugbyAssistant --package-version=3.10 --msgid-bugs-address=fproperzi@gmail.com -i -F -j -o ./it_IT/main.po ../*.php
xgettext -k_e --from-code=UTF-8 --package-name=RugbyAssistant --package-version=3.10 --msgid-bugs-address=fproperzi@gmail.com -i -F -j -o ./af_ZA/main.po ../*.php
xgettext -k_e --from-code=UTF-8 --package-name=RugbyAssistant --package-version=3.10 --msgid-bugs-address=fproperzi@gmail.com -i -F -j -o ./en_GB/main.po ../*.php
xgettext -k_e --from-code=UTF-8 --package-name=RugbyAssistant --package-version=3.10 --msgid-bugs-address=fproperzi@gmail.com -i -F -j -o ./fr_FR/main.po ../*.php
xgettext -k_e --from-code=UTF-8 --package-name=RugbyAssistant --package-version=3.10 --msgid-bugs-address=fproperzi@gmail.com -i -F -j -o ./es_AR/main.po ../*.php

echo -- Trasformo in .so 
#msgfmt main.po -o main-`date +%s`.mo
msgfmt it_IT/main.po -o ./it_IT/LC_MESSAGES/main.mo
msgfmt af_ZA/main.po -o ./af_ZA/LC_MESSAGES/main.mo
msgfmt en_GB/main.po -o ./en_GB/LC_MESSAGES/main.mo
msgfmt fr_FR/main.po -o ./fr_FR/LC_MESSAGES/main.mo
msgfmt es_AR/main.po -o ./es_AR/LC_MESSAGES/main.mo

echo -- chown files 
chown root:nginx ./it_IT/LC_MESSAGES/main.mo
chown root:nginx ./af_ZA/LC_MESSAGES/main.mo
chown root:nginx ./en_GB/LC_MESSAGES/main.mo
chown root:nginx ./fr_FR/LC_MESSAGES/main.mo
chown root:nginx ./es_AR/LC_MESSAGES/main.mo


echo -- service nginx,php-fpm restart
sudo service nginx reload
sudo service php-fpm reload

#sudo service nginx restart
#echo -- stop nginx
#sudo service nginx stop
#echo -- wait 5 seconds
#sleep 5
#echo -- start nginx
#sudo service nginx start