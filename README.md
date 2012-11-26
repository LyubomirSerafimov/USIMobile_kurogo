# Project USIMobile; Kurogo based server for USIMobile app

This project provides a Kurogo based server for the mobile application of
the Università della Svizzera Italiana [USI](http://www.usi.ch). It is based on 
[Kurogo framework](http://kurogo.org/docs)


This project has been carried out as part of the program "AAA/SWITCH – e-Infrastructure for 
e-Science" lead by SWITCH, the Swiss National Research and Education Network, and was 
supported by funds from the State Secretariat for Education and Research.

For further information please visit:
* http://www.elearninglab.org/progetti/mobileuni-app?lang=en

## Online Guide

Kurogo developer's guide can be found here:

* [Kurogo Mobile Web](http://kurogo.org/docs/mw/)

## Quick Setup and Requirements

Kurogo is a PHP application. It is currently qualified for use with

* Apache 2.x
    * mod_rewrite, and .htaccess support (AllowOverride)
* IIS 7.5
   * URL Rewrite Module 2.0
* PHP 5.2 (5.3 recommended) or higher with the following extensions
    * zlib, xml, dom, json, pdo, mbstring, LDAP, curl

## Installation
After the Kurogo Framework setup clone this repository in your site directory.

* cd KUROGO_HOME_DIRECTORY/site
* git clone git://github.com/arael/USIMobile_kurogo.git usi

Open your KUROGO_HOME_DIRECTORY/config/kurogo.ini file and set: ACTIVE_SITE = "usi"

Refer to the Kurogo online guide for troubleshooting.
