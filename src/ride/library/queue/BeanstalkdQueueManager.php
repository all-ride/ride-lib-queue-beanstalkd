<?php

namespace ride\library\queue;

use Beanstalk\Client;

use ride\library\queue\exception\QueueException;
use ride\library\queue\job\QueueJob;
use ride\library\queue\QueueManager;

use \Exception;

/**
 * Beanstalkd implementation for the queue manager
 */
class BeanstalkdQueueManager implements QueueManager {

    /**
     * Constructs a new queue manager
     * @param Beanstalk\Client $client Instance of the Beanstalk client
     * @param integer $ttr Time to run, number of seconds to allow a worker to
	 * run this job. The minimum ttr is 1
     * @return null
     */
    public function __construct(Client $client, $ttr = 3600) {
        $this->client = $client;
        $this->ttr = $ttr;
    }

    /**
     * Gets the status of the different queues
     * @return array Array with the name of the queue as key and the number of
     * queued slots as value
     */
    public function getQueueStatus() {
        $status = array();

        $tubes = $this->client->listTubes();
        foreach ($tubes as $tube) {
            $stats = $this->client->statsTube($tube);

            $status[$tube] = $stats['current-jobs-delayed'] + $stats['current-jobs-ready'];
        }

        return $status;
    }

    /**
     * Gets the jobs for the provided queue
     * @param string $queue Name of the queue
     * @return array Array with the QueueJobStatus objects
     */
    public function getQueueJobStatuses($queue) {
        // impossible to implement without a second data source
        return array();
    }

    /**
     * Gets the status of a job in the queue
     * @param string $id Id of the job in the queue
     * @return null|QueueJobStatus Null if the job is finished, the status of
     * the job otherwise
     */
    public function getQueueJobStatus($id) {
        $data = $this->client->peek($id);
        if ($data === false) {
            return null;
        }

        $stats = $this->client->statsJob($id);

        $queueJobStatus = unserialize($data['body']);
        $queueJobStatus->setId($data['id']);

        switch ($stats['state']) {
            case 'buried':
                $queueJobStatus->setError();

                break;
            case 'reserved':
                $queueJobStatus->setProgress();

                break;
            case 'ready':
            case 'delayed':
                $queueJobStatus->setWaiting();

                break;
        }

        return $queueJobStatus;
    }

    /**
     * Pushes a job to the queue
     * @param \ride\library\queue\job\QueueJob $queueJob Instance of the job
     * @param integer $dateScheduled Timestamp from which the invokation is
     * possible (optional)
     * @return QueueJobStatus Status of the job
     */
    public function pushJobToQueue(QueueJob $queueJob, $dateScheduled = null) {
        if (!$queueJob->getQueue()) {
            throw new QueueException('Could not push job to queue: no queue specified');
        }

        $queueJobStatus = new BeanstalkdQueueJobStatus($queueJob);

        $priority = $this->getPriority($queueJob);
        $delay = $this->getDelay($dateScheduled);
        $ttr = $this->ttr;
        $body = serialize($queueJobStatus);

        $this->client->useTube($queueJob->getQueue());
        $id = $this->client->put($priority, $delay, $ttr, $body);

        return $this->getQueueJobStatus($id);
    }

    /**
     * Pops a job from the queue (FIFO) and marks it as in progress
     * @param string $queue Name of the queue
     * @return QueueJobStatus|null Status of the first job in the provided queue
     * or null if the queue is empty
     */
    public function popJobFromQueue($queue) {
        $this->client->watch($queue);

        $data = $this->client->reserve(0);
        if ($data === false) {
            return null;
        }

        $queueJobStatus = unserialize($data['body']);
        $queueJobStatus->setId($data['id']);

        return $queueJobStatus;
    }

    /**
     * Updates the status of a job
     * @param integer $id Id of the job status
     * @param string $destription Description of the progress
     * @param string $status Status code
     * @throws \ride\library\queue\exception\QueueException
     */
    public function updateStatus($id, $description, $status = null) {
        // impossible to implement fully without a second data source
        // we can bury error jobs so they won't be invoked again
        if ($status === QueueManager::STATUS_ERROR) {
            $this->client->bury($id, $this->getPriority());
        }
    }

    /**
     * Reschedule a existing job
     * @param \ride\library\queue\job\QueueJob $queueJob Instance of the job
     * @param integer $dateScheduled Timestamp from which the invokation is
     * possible
     * @return null
     */
    public function rescheduleJob(QueueJob $queueJob, $dateScheduled) {
        $priority = $this->getPriority($queueJob);
        $delay = $this->getDelay($dateScheduled);

        $this->client->release($queueJob->getJobId(), $priority, $delay);
    }

    /**
     * Finishes a job
     * @param \ride\library\queue\job\QueueJob $queueJob Instance of the job
     * @return null
     */
    public function finishJob(QueueJob $queueJob) {
        if (!$this->client->delete($queueJob->getJobId())) {
            throw new QueueException('Could not finish the job: job #' . $queueJob->getJobId() . ' could not be deleted');
        }
    }

    /**
     * Gets the priority of a queue job
     * @param \ride\library\queue\job\QueueJob $queueJob Instance of the job
     * @return integer
     */
    protected function getPriority(QueueJob $job = null) {
        if (!$job || $job->getPriority() === null) {
            return 10;
        }

        return $job->getPriority();
    }

    /**
     * Calculates the delay for the provided schedule date
     * @param integer $dateScheduled Timestamp of the schedule date
     * @return integer
     */
    protected function getDelay($dateScheduled = null) {
        if ($dateScheduled) {
            return $dateScheduled - time();
        }

        return 0;
    }

}
