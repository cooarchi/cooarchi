<?php

declare(strict_types=1);

namespace CooarchiApp\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question;
use function file_get_contents;
use function file_put_contents;
use function sprintf;
use function str_replace;

class SetupCommand extends Command
{
    private const NO = 'no';
    private const YES = 'yes';

    /**
     * @var string
     */
    protected static $defaultName = 'cooArchi:setup';

    /**
     * @var string
     */
    private $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;

        parent::__construct(self::class);
    }

    protected function configure() : void
    {
        $this->setDescription('Configure cooArchi installation')
            ->setHelp('This command allows you to setup cooArchi backend app and configure DB connection');
    }

    public function run(InputInterface $input, OutputInterface $output) : int
    {
        //$input->setInteractive(true);

        $welcomeMessage = 'Hi, great you found me! Will try to help you now to get your cooArchi up and running.';
        $output->writeln(sprintf('<comment>%s</comment>', $welcomeMessage));

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        try {
            $question = new Question\ConfirmationQuestion('<info>Ready?</info>', true, '/^(y|j)/i');
            if ($questionHelper->ask($input, $output, $question) === false) {
                $output->writeln(sprintf('<comment>%s</comment>', 'Bye!'));
                return Command::FAILURE;
            }

            $output->writeln('First step: Database (MySQL) configuration');
            $question = new Question\Question('<comment>Provide database host: </comment>');
            $dbHost = $questionHelper->ask($input, $output, $question);
            $question = new Question\Question('<comment>Provide database user: <comment>');
            $dbUser = $questionHelper->ask($input, $output, $question);
            $question = new Question\Question('<comment>Provide database password: <comment>');
            $dbPassword = $questionHelper->ask($input, $output, $question);
            $question = new Question\Question('<comment>Provide database name: <comment>');
            $dbName = $questionHelper->ask($input, $output, $question);

            $output->writeln('Second step: cooArchi Settings');
            $question = new Question\Question('<comment>Provide name for cooArchi: </comment>');
            $cooArchiName = $questionHelper->ask($input, $output, $question);

            $question = new Question\ChoiceQuestion(
                '<comment>cooArchi will allow core elements: </comment>',
                [self::NO, self::YES]
            );
            $allowCoreElementsAnswer = $questionHelper->ask($input, $output, $question);

            $question = new Question\ChoiceQuestion(
                '<comment>cooArchi will allow kollektivista to add relations: </comment>',
                [self::NO, self::YES]
            );
            $kollektivistaCanAddRelationsAnswer = $questionHelper->ask($input, $output, $question);

            $question = new Question\ChoiceQuestion(
                '<comment>cooArchi will be public viewable: </comment>',
                [self::NO, self::YES]
            );
            $isPublicReadableAnswer = $questionHelper->ask($input, $output, $question);

            $question = new Question\ChoiceQuestion(
                '<comment>cooArchi will be public writeable: </comment>',
                [self::NO, self::YES]
            );
            $isPublicWriteableAnswer = $questionHelper->ask($input, $output, $question);

            $allowCoreElements = $this->getBooleanAnswerStringValue($allowCoreElementsAnswer);
            $kollektivistaCanAddRelations = $this->getBooleanAnswerStringValue($kollektivistaCanAddRelationsAnswer);
            $isPublicReadable = $this->getBooleanAnswerStringValue($isPublicReadableAnswer);
            $isPublicWriteable = $this->getBooleanAnswerStringValue($isPublicWriteableAnswer);

            $question = new Question\Question('<comment>Backend URL: </comment>');
            $backendUrl = $questionHelper->ask($input, $output, $question);

            $question = new Question\Question('<comment>Frontend URL: </comment>');
            $frontendUrl = $questionHelper->ask($input, $output, $question);

            $configTemplate = file_get_contents(sprintf('%s/config/autoload/local.php.setup', $this->basePath));

            $cooArchiName = str_replace("'", '', $cooArchiName);

            $configTemplate = str_replace('{{dbHost}}', $dbHost, $configTemplate);
            $configTemplate = str_replace('{{dbName}}', $dbName, $configTemplate);
            $configTemplate = str_replace('{{dbUser}}', $dbUser, $configTemplate);
            $configTemplate = str_replace('{{dbPassword}}', $dbPassword, $configTemplate);
            $configTemplate = str_replace('{{cooArchiName}}', $cooArchiName, $configTemplate);
            $configTemplate = str_replace('{{allowCoreElements}}', $allowCoreElements, $configTemplate);
            $configTemplate = str_replace('{{kollektivistaCanAddRelations}}', $kollektivistaCanAddRelations, $configTemplate);
            $configTemplate = str_replace('{{isPublicReadable}}', $isPublicReadable, $configTemplate);
            $configTemplate = str_replace('{{isPublicWriteable}}', $isPublicWriteable, $configTemplate);
            $configTemplate = str_replace('{{backendUrl}}', $backendUrl, $configTemplate);
            $configTemplate = str_replace('{{frontendUrl}}', $frontendUrl, $configTemplate);

            $configFilePath = sprintf('%s/config/autoload/local.php', $this->basePath);
            file_put_contents($configFilePath, $configTemplate);

            $output->writeln('<info>Finished creation of app config!</info>');
            $output->writeln(
                sprintf('<comment>Please verify your settings inside file: %s</comment>', $configFilePath)
            );
        } catch (\Exception $exception) {
            $output->writeln(sprintf('<error>An error occurred: %s</error>', $exception->getMessage()));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function getBooleanAnswerStringValue(string $answer) : string
    {
        if ($answer === self::YES) {
            return 'true';
        }

        return 'false';
    }
}
