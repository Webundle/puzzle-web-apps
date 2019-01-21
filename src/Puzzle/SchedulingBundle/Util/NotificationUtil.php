<?php

namespace Puzzle\SchedulingBundle\Util;

use Puzzle\SchedulingBundle\Entity\Notification;

class NotificationUtil
{
    public static function constructTargetCriteria($targetObject){
        $targetClass = get_class($targetObject);
        $targetParts = explode('\\', $targetClass);
        
        $targetAppName = strtolower(implode(',', str_replace("Bundle", "", preg_grep("#Bundle#", $targetParts))));
        $targetEntityName = strtolower($targetParts[count($targetParts) - 1]);
        $targetEntityId = $targetObject->getId();
        
        return [
            'targetAppName' => $targetAppName,
            'targetEntityName' => $targetEntityName,
            'targetEntityId' => $targetEntityId
        ];
    }
    
    public static function mergeDataForScheduling(array $data, $targetObject, string $commandName, array $commandArgs = null, string $channel = null){
        $data = array_merge($data, self::constructTargetCriteria($targetObject));
        
        $data['notificationCommand'] = $commandName;
        $data['notificationCommandArgs'] = $commandArgs;
        $data['notificationChannel'] = $channel !== null ? $channel : Notification::CHANNEL_IN_APP;
        $data['notificationUnity'] = Notification::UNITY_MINUTE;
        $data['notificationIntervale'] = 1;
        
        return $data;
    }
}