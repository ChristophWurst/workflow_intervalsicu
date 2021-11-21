<?php

declare(strict_types=1);

namespace OCA\WorkflowIntervalsicu\Listener;

use OCA\WorkflowIntervalsicu\Workflow\UploadOperation;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\WorkflowEngine\Events\RegisterOperationsEvent;

class RegisterFlowOperationsListener implements IEventListener {

	private UploadOperation $uploadOperation;

	public function __construct(UploadOperation $uploadOperation) {
		$this->uploadOperation = $uploadOperation;
	}

	/**
	 * @param RegisterOperationsEvent $event
	 */
	public function handle(Event $event): void {
		$event->registerOperation($this->uploadOperation);
	}

}
