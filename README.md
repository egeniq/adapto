Adapto XX
======

Adapto is a small PHP framework for creating data management applications with minimal code, and is especially suited to:

* Enterprise CRUD applications
* Micro CMS systems
* Data management applications, including glue and reporting

Adapto takes original concepts first proven in the [ATK framework](http://www.atk-framework.com) (created by Ivo Jansch and [ibuildings](http://www.ibuildings.nl/)) to the next level. The project is led by Egeniq, the company that was founded by two ATK veterans Peter Verhage and Ivo Jansch. 

Adapto concentrates on what makes it unique, and uses Zend Framework for caching, view rendering, database connectivity, etc.  

What makes Adapto different is:

* Adapto provides basic CRUD functionality for relational databases and other data sources with only a few lines of code
* Adapto is fully compatible with Zend Framework 2, and integrates nicely into ZF applications

Adapto documentation is maintained in the project wiki on GitHub.  To get started, you should review the following wiki pages:

* [Roadmap](https://github.com/egeniq/adapto/wiki/Roadmap) - for current project priorities and direction
* [Development Process](https://github.com/egeniq/adapto/wiki/Development-Process) - for developing Adapto (**not** "developing _with_ Adapto" - that is yet to come)

The [SourceForge Adapto developers mailing list](https://lists.sourceforge.net/lists/listinfo/adapto-developers) is for day-to-day development discussions and decisions, although traffic is fairly low at present. [Join the mailing list](https://lists.sourceforge.net/lists/listinfo/adapto-developers) to stay current with the project, and then become more involved when the right is right.

Summary of Adapto Development Projects
--------------------------------------
* [Github Code repository, issue tracker and documentation Wiki](https://github.com/egeniq/adapto/)
* [SourceForge Developer mailing list and release downloads](http://www.sourceforge.net/projects/adapto )

License & Copyright
===================
Adapto is distributed under the BSD license. Large portions of the codebase are copyright 2000-2011 Ibuildings & Ivo Jansch, new code is copyright 2012 Egeniq (founded by Ivo Jansch after leaving ibuildings).

Getting Started
===============
Adapto is pre-alpha, but the basic demo is functional so you can start understanding Adapto now. To get started, follow these steps:

1. Clone the repository, e.g. `git clone git://github.com/egeniq/adapto.git`
2. Make sure you have all required dependencies, by executing `git submodule update --init --recursive`
3. Configure your webserver so that adapto/public serves as the document root.
4. Browse the site. You should now see a default welcome page and a set of menu items demonstrating the framework capabilities.

FAQ
===

### What does the name Adapto mean?

Adapto is short for ADvanced APlication TOolkit. It's also highly adaptable. 
