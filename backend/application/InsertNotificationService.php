<?php
namespace Backend\Application;

use Backend\Domain\Entities\NotificationData;
use Backend\Infrastructure\NotificationsRepository;

class InsertNotificationService
{
    private NotificationsRepository $repo;
    public function __construct(NotificationsRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(NotificationData $notificationData): array
    {
      $success = $this->repo->insert($notificationData);
        
      if (!$success) {
        return [
          'success' => false, 
          'message' => 'Failed to insert notification.'
        ];
      }

      return [
        'success' => $success,
        'message' => 'Notification inserted successfully.',
      ];
    }
}
