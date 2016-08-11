<?php

namespace ride\library\queue;

use ride\library\queue\QueueJobStatus;
use ride\library\queue\QueueManager;

/**
 * Status of a Beanstalkd queue job
 */
class BeanstalkdQueueJobStatus implements QueueJobStatus {

    /**
     * Id of the job
     * @var integer
     */
    private $id;

    /**
     * Class name of the QueueJob instance
     * @var string
     */
    private $className;

    /**
     * Instance of the queue job
     * @var \ride\library\queue\job\QueueJob
     */
    private $queueJob;

    /**
     * Status of this job
     * @var string
     */
    private $status;

    /**
     * Timestamp of the creation time
     * @var integer
     */
    private $dateAdded;

    /**
     * Constructs a new beanstalks queue job status
     * @param \ride\library\queue\job\QueueJob
     * @return null
     */
    public function __construct($queueJob) {
        $this->queueJob = $queueJob;
        $this->className = get_class($this->queueJob);
        $this->dateAdded = time();
    }

    /**
     * Sets the id of this job
     * @param string $id
     * @return null
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Gets the id of the job
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Gets the name of the queue
     * @return string
     */
    public function getQueue() {
        return $this->queueJob->getQueue();
    }

    /**
     * Gets the class name of the queue job
     * @return string
     */
    public function getClassName() {
        return $this->className;
    }

    /**
     * Gets the queue job
     * @return \ride\library\queue\QueueJob
     */
    public function getQueueJob() {
        $this->queueJob->setJobId($this->getId());

        return $this->queueJob;
    }

    /**
     * Sets the status to waiting
     * @return null
     */
    public function setWaiting() {
        $this->status = QueueManager::STATUS_WAITING;
    }

    /**
     * Sets the status to progress
     * @return null
     */
    public function setProgress() {
        $this->status = QueueManager::STATUS_PROGRESS;
    }

    /**
     * Sets the status to error
     * @return null
     */
    public function setError() {
        $this->status = QueueManager::STATUS_ERROR;
    }

    /**
     * Gets the status code
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Gets a detailed description of the status
     * @return string
     */
    public function getDescription() {
        return null;
    }

    /**
     * Gets the slot number
     * @return integer
     */
    public function getSlot() {
        return -1;
    }

    /**
     * Gets the total number of slots
     * @return integer
     */
    public function getSlots() {
        return -1;
    }

    /**
     * Gets the added date
     * @return integer Timestamp
     */
    public function getDateAdded() {
        return $this->dateAdded;
    }

    /**
     * Gets the schedule date
     * @return integer|null Timestamp
     */
    public function getDateScheduled() {
        return $this->dateAdded;
    }

}
