Tutorial: Create a New Bake Command
###################################

The Bake console can be used for generating any file or set of files based
on a twig template or templates. This tutorial demonstrates how to create a
new Bake console command.

This new Bake command ``value_object`` generates a partial implementation of
a `ValueObject <https://martinfowler.com/bliki/ValueObject.html>`_ class.
You might use this Bake command, for example, to generate the skeleton code
for a class that follows a specific design pattern.

Our purpose is to show the minimum needed to get a new Bake command working.
With that foundation you can more easily explore the official Bake commands
at `<https://github.com/cakephp/bake/tree/master/src/Command>`_.

Your new Bake console command is part of a CakePHP plugin and must be run
inside a CakePHP project. Therefore we'll start by creating a new empty
CakePHP project, then generating a plugin using ``bake``.

Project Setup
=============

Create a new CakePHP project according to the installation instructions at
`Installation <https://book.cakephp.org/4/en/installation.html>`_:

``composer create-project --prefer-dist cakephp/app:4.* new-bake-command``

Create the new plugin:

``bin/cake bake plugin ValueObject``

Command and Template
====================

You can generate the new command with, for example,
``bin/cake bake command --plugin ValueObject ValueObject``. See
`Command Objects <https://book.cakephp.org/4/en/console-commands/commands.html>`_
for information on writing new Commands, including new Bake commands. Once
the class is generated, change it to extend ``Bake\Command\BakeCommand`` rather
than ``Cake\Command\Command``.

The Bake console uses the `Twig template engine <https://twig.symfony.com/>`_.
All Bake console templates are in the GitHub repository at
`<https://github.com/cakephp/bake/tree/master/templates/bake>`_. Place your
templates in your plugin under ``templates/bake``.

Note that your templates do not need to be in the same plugin as your new Bake
console command(s). See
`Creating a Bake Theme <https://book.cakephp.org/bake/2/en/development.html#creating-a-bake-theme>`_.

The finished source code is available at
`<https://github.com/ewbarnard/BakeSample>`_.
