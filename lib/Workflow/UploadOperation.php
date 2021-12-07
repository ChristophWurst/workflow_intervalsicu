<?php

declare(strict_types=1);

namespace OCA\WorkflowIntervalsicu\Workflow;

use OCA\WorkflowIntervalsicu\Service\Uploader;
use OCP\EventDispatcher\Event;
use OCP\Files\File;
use OCP\Files\Node;
use OCP\WorkflowEngine\IManager;
use OCP\WorkflowEngine\IOperation;
use OCP\WorkflowEngine\IRuleMatcher;

class UploadOperation implements IOperation {

	private Uploader $uploader;

	public function __construct(Uploader $uploader) {
		$this->uploader = $uploader;
	}

	public function getDisplayName(): string {
		return 'Upload to intervals.icu';
	}

	public function getDescription(): string {
		return 'Uploads a file to intervals.icu';
	}

	public function getIcon(): string {
		return '';
	}

	public function isAvailableForScope(int $scope): bool {
		return $scope === IManager::SCOPE_USER;
	}

	public function validateOperation(string $name, array $checks, string $operation): void {
		// TODO: Implement validateOperation() method.
	}

	public function onEvent(string $eventName, Event $event, IRuleMatcher $ruleMatcher): void {
		if ($eventName !== '\OCP\Files::postCreate') {
			return;
		}

		/** @var Node $node */
		$node = $event->getSubject();
		if ($node instanceof File) {
			$this->uploader->upload($node);
		}
	}
}
