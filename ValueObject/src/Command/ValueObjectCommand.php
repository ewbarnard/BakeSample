<?php
declare(strict_types=1);

namespace ValueObject\Command;

use Bake\Command\BakeCommand;
use Bake\Utility\TemplateRenderer;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Plugin;
use Cake\Utility\Inflector;
use InvalidArgumentException;

class ValueObjectCommand extends BakeCommand
{
    /* Do not conflict with CommonOptionsTrait.php options */
    private const OPTIONS = [
        'destination' => ['short' => 'd', 'help' => 'Destination folder under src/', 'default' => 'ValueObject'],
        'getters' => ['short' => 'g', 'help' => 'List of fields separated by commas', 'default' => 'id,name'],
    ];

    /** @var \Cake\Console\Arguments */
    private $args;
    /** @var \Cake\Console\ConsoleIo */
    private $io;

    /** @var string */
    private $destination;
    /** @var string[] */
    private $getters;
    /** @var string */
    private $valueObjectClass;

    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = $this->_setCommonOptions($parser);
        $parser->addOptions(self::OPTIONS);
        $parser->addArgument('name', [
            'help' => 'Class name of the value object being created', 'required' => true,
        ]);

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $this->args = $args;
        $this->io = $io;

        $this->extractCommonProperties($args);
        $this->setArgs();
        $this->bakeValueObject();

        return 0;
    }

    private function setArgs(): void
    {
        $this->destination = (string)$this->args->getOption('destination');
        $this->getters = array_map('trim', explode(',', $this->args->getOption('getters')));
        $this->valueObjectClass = (string)$this->args->getArgument('name');

        if ((string)$this->theme !== '') {
            return;
        }
        // No theme specified - locate a theme containing our template
        $template = 'templates' . DS . 'bake' . DS . 'ValueObject' . DS . 'value_object.twig';
        foreach (Plugin::loaded() as $plugin) {
            $path = Plugin::path($plugin);
            if (file_exists($path . $template)) {
                $this->theme = $plugin;

                return;
            }
        }
        throw new InvalidArgumentException("No theme found containing $template");
    }

    private function bakeValueObject(): void
    {
        $renderer = new TemplateRenderer($this->theme);

        // Set up data used by template
        $methods = [];
        foreach ($this->getters as $field) {
            $methods[$field] = Inflector::camelize($field);
        }
        $data = [
            'destination' => $this->destination,
            'name' => $this->valueObjectClass,
            'methods' => $methods,
        ];

        // Do not include .twig filename extension when passing in template name
        // Path is relative to plugins/(plugin-name)/templates/bake
        $out = $renderer->generate('ValueObject' . DS . 'value_object', $data);

        // Write output file
        $path = $this->getPath($this->args) . $this->destination;
        $filename = $path . DS . $this->valueObjectClass . 'ValueObject.php';
        $this->io->out(PHP_EOL . sprintf('Baking %s class for %s...', $this->destination, $this->valueObjectClass), 1,
            ConsoleIo::QUIET);
        $this->io->createFile($filename, $out, $this->args->getOption('force'));

        // Delete placeholder if any
        $emptyFile = $path . DS . '.gitkeep';
        $this->deleteEmptyFile($emptyFile, $this->io);
    }
}
