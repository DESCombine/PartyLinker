# PartyLinker

  

Maglia Danilo

Sanchi Emanuele

Severi Tommaso

  

Repository for a social network for Web Technologies course

  

docs: https://docs.google.com/document/d/1C_xu4oAYL0aEz9kDHxQVmgzb6EMx2ZMC8hJkvgZOxFw/edit?hl=it&pli=1

  

# Setup for local development

  

I'll assume you already have xampp installed and running

  

First of all you should setup some enviroment variable on your apache server

Find the `<Directory>` tag in the httpd.conf file and write the following

    SetEnv PL_SERVERNAME [server ip]
    SetEnv PL_USERNAME [db username]
    SetEnv PL_PASSWORD [db password]
    SetEnv PL_DBNAME [db name]
    SetEnv PL_ROOTDIRECTORY [directory of this repository with included / at the end]

