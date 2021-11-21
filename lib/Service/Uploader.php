<?php

declare(strict_types=1);

namespace OCA\WorkflowIntervalsicu\Service;

use Exception;
use OCA\WorkflowIntervalsicu\AppInfo\Application;
use OCP\Files\File;
use OCP\Http\Client\IClientService;
use OCP\IConfig;
use Psr\Log\LoggerInterface;

class Uploader {

	private IConfig $config;
	private IClientService $httpService;
	private LoggerInterface $logger;

	public function __construct(IConfig         $config,
								IClientService  $httpService,
								LoggerInterface $logger) {
		$this->config = $config;
		$this->httpService = $httpService;
		$this->logger = $logger;
	}

	public function upload(File $file): void {
		$owner = $file->getOwner();

		$client = $this->httpService->newClient();
		$athleteId = $this->config->getUserValue(
			$owner->getUID(),
			Application::APP_ID,
			'athlete_id',
			null,
		);
		if ($athleteId === null) {
			$this->logger->warning('User {user} does not have an athlete ID set', [
				'user' => $owner->getUID(),
			]);
			return;
		}
		$apiKey = $this->config->getUserValue(
			$owner->getUID(),
			Application::APP_ID,
			'api_key',
			null,
		);
		if ($apiKey === null) {
			$this->logger->warning('User {user} does not have an API key set', [
				'user' => $owner->getUID(),
			]);
			return;
		}

		try {
			$response = $client->post("https://intervals.icu/api/v1/athlete/$athleteId/activities", [
				'auth' => [
					'API_KEY',
					$apiKey,
				],
				'multipart' => [
					[
						'name' => 'file',
						'contents' => $file->getContent(),
						'filename' => $file->getName(),
					],
				],
			]);
		} catch (Exception $e) {
			$this->logger->error('intervals.icu upload failed: ' . $e->getMessage(), [
				'exception' => $e,
			]);

			return;
		}

		$this->logger->debug('File {file} uploaded successfully', [
			'file' => $file->getName(),
		]);
	}

}
