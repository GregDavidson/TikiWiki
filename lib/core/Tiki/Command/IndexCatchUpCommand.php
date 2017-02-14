<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: IndexCatchUpCommand.php 58747 2016-05-31 23:00:07Z lindonb $

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IndexCatchUpCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('index:catch-up')
			->setDescription('Catch-up on incremental indexing.')
			->addArgument(
				'amount',
				InputArgument::OPTIONAL,
				'Amount of queue entries to catch-up on',
				10
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$amount = (int) $input->getArgument('amount');

		$unifiedsearchlib = \TikiLib::lib('unifiedsearch');

		try {
			$output->writeln('Started processing queue...');

			$result = $unifiedsearchlib->processUpdateQueue($amount);

			$count = $unifiedsearchlib->getQueueCount();

			$output->writeln('Processing completed. Amount remaining: ' . $count);
		} catch (ZendSearch\Lucene\Exception\ExceptionInterface $e) {

			$msg = tr('Search index could not be updated: %0', $e->getMessage());
			\Feedback::error($msg, 'session');
		}
		
		$errors = \Feedback::get();
		if (is_array($errors)){
			foreach ($errors as $message) {
				$output->writeln("<error>$message</error>");
			}
		}
	}
}
