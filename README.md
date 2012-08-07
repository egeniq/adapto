Adapto
======

Adapto is a small PHP framework targeted at creating data management applications with minimal amounts of code. 

It has the following major features:

* Compatible with Zend Framework 2 (integrates nicely into ZF applications)
* Basic CRUD functionality for relational databases and other data sources with only a few lines of code

Adapto is suitable for a number of use cases:

* Micro CMS systems
* Data management applications

Adapto is essentially a Zend Framework 2 port of the classic (and by now outdated) ATK framework (http://atk-framework.com). 

License & Copyright
===================
Adapto is distributed under a BSD license. 

Large portions of the codebase stem from the ATK framework and are copyright 2000-2011 Ibuildings & Ivo Jansch.
New portions of the codebase are copyright 2012 Egeniq.

Getting Started
===============
The Adapto repository contains a demo application. To get started, perform the following steps:

1. Clone the repository, e.g. `git clone git://github.com/egeniq/adapto.git`
2. Make sure you have all required dependencies, by executing `git submodule update --init --recursive`
3. Configure your webserver so that adapto/public serves as the document root.
4. Browse the site. You should now see a default welcome page and a set of menu items demonstrating the framework capabilities.

FAQ
===

## What does the name adapto mean?

Adapto is short for ADvanced APlication TOolkit. It's also highly adaptable. 

