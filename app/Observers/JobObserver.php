<?php

namespace App\Observers;

use App\Models\Job;

class JobObserver
{
    public function deleting(Job $job)
    {
    	$elevations = $job->elevations;

    	// Delete elevations associated with the job
    	if ($elevations->count()) {

    		foreach ($elevations as $elevation) {
	    		$elevation->images()->delete();
	    		$elevation->delete();
	    	}
    	}

    	// Delete rooms associated with the job
		foreach ($job->rooms as $room) {
    		$room->images()->delete();
    		$room->delete();
    	}
    }
}
