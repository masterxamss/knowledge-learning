<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;

/**
 * SetupDatabaseCommand is responsible for automating the database setup process in the Symfony application.
 *
 * This includes dropping and recreating the database, running migrations, loading fixtures, and clearing the cache.
 */
#[AsCommand(
  name: 'app:setup-database',
  description: 'This command is responsible for automating the process of configuring and initializing the database for the Symfony application.',
)]
class SetupDatabaseCommand extends Command
{
  /**
   * The default name for the command.
   */
  protected static $defaultName = 'app:setup-database';

  private $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
    parent::__construct();
  }

  /**
   * Executes the database setup commands.
   *
   * @param InputInterface $input The input interface.
   * @param OutputInterface $output The output interface.
   * @return int The command exit code.
   */
  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    // Check if the database already has data or if fixtures need to be loaded
    $connection = $this->entityManager->getConnection();
    $result = $connection->fetchAssociative('SELECT COUNT(*) AS count FROM doctrine_migration_versions');
    $migrationCount = (int) $result['count'];

    // Define the commands to be executed for setting up the database
    $commands = [
      ['php', 'bin/console', 'doctrine:database:drop', '--force', '--if-exists'],
      ['php', 'bin/console', 'doctrine:database:create'],
      ['php', 'bin/console', 'doctrine:migrations:migrate', '--no-interaction'],
    ];

    // Run the basic database setup commands
    foreach ($commands as $command) {
      $process = new Process($command);
      $process->setTimeout(null);
      $process->run(function ($type, $buffer) use ($output) {
        $output->write($buffer);
      });

      if (!$process->isSuccessful()) {
        $output->writeln('<error>Error when executing ' . implode(' ', $command) . '</error>');
        return Command::FAILURE;
      }
    }

    // Only load fixtures if there are no migrations applied yet
    if ($migrationCount === 0) {
      $output->writeln("[INFO] Loading fixtures...");
      $process = new Process(['php', 'bin/console', 'doctrine:fixtures:load', '--no-interaction']);
      $process->run(function ($type, $buffer) use ($output) {
        $output->write($buffer);
      });

      if (!$process->isSuccessful()) {
        $output->writeln('<error>Error when loading fixtures.</error>');
        return Command::FAILURE;
      }
    } else {
      $output->writeln("[INFO] Migrations already applied, skipping fixtures loading.");
    }

    // Clear cache
    $output->writeln("[INFO] Clearing cache...");
    $process = new Process(['php', 'bin/console', 'cache:clear']);
    $process->run(function ($type, $buffer) use ($output) {
      $output->write($buffer);
    });

    return Command::SUCCESS;
  }
}
