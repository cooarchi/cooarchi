<?php

declare(strict_types=1);

namespace CooarchiApp\Command;

use CooarchiApp\Authentication\Adapter;
use CooarchiApp\ConfigProvider;
use CooarchiEntities\User;
use CooarchiQueries;
use Doctrine\ORM\EntityManager;
use Exception;
use InvalidArgumentException;
use Laminas\Crypt\Password\Bcrypt;
use LogicException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question;
use function mb_strlen;
use function sprintf;

class CreateAdministrataCommand extends Command
{
    private const USERNAME = 'administrata42';

    /**
     * @var string
     */
    protected static $defaultName = 'cooArchi:create-administrata';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CooarchiQueries\FindUser
     */
    private $findUserQuery;

    public function __construct(EntityManager $entityManager, CooarchiQueries\FindUser $findUserQuery)
    {
        $this->entityManager = $entityManager;
        $this->findUserQuery = $findUserQuery;

        parent::__construct(self::class);
    }

    protected function configure() : void
    {
        $this->setDescription('Create cooArchi administratA user')
            ->setHelp('This command allows you to create first cooArchi administratA user');
    }

    public function run(InputInterface $input, OutputInterface $output) : int
    {
        try {
            $userExistsCheck = $this->findUserQuery->byName(self::USERNAME);
            if ($userExistsCheck !== null) {
                throw new LogicException('User already exists! Bye');
            }

            $output->writeln('<info>We create your first administratA account now:</info>');

            /** @var QuestionHelper $questionHelper */
            $questionHelper = $this->getHelper('question');

            $question = new Question\Question('<comment>Provide a safe password: </comment>');
            $userPassword = $questionHelper->ask($input, $output, $question);
            if (mb_strlen($userPassword, ConfigProvider::ENCODING) < 12) {
                throw new InvalidArgumentException('Safe password means larger then 12 chars!');
            }


            $bcrypt = new Bcrypt();
            $bcrypt->setCost(Adapter::PASSWORD_COST);
            $userPassword = $bcrypt->create($userPassword);
            $userRecord = new User(self::USERNAME, $userPassword, User::ROLE_ADMINISTRATA);

            $this->entityManager->persist($userRecord);
            $this->entityManager->flush();

            $output->writeln(
                sprintf(
                    '<info>Finished creation of administratA user - name is: %s</info>',
                    $userRecord->getName()
                )
            );
        } catch (Exception $exception) {
            $output->writeln(sprintf('<error>An error occurred: %s</error>', $exception->getMessage()));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
