<?php
/**
 * @author : Muhammad Saeed <msaeed.iu@gmail.com>
 * @since  : 3-April-2021
 * @todo   : This is helper functions file.
*/


/***************************** App helper ********************************************/
	if (!function_exists('app_name')) {
		/**
		 * Helper to grab the application name.
		 *
		 * @return mixed
		 */
		function app_name()
		{
		    return config('app.name');
		}
	}

	if (!function_exists('getDefaultImageUrl')) {
		/**
		 * Helper to get Default Images.
		 *
		 * @return mixed
		 */
		function getDefaultImageUrl($model)
		{
			if($model == 'job')
		    	return 'default_images/job.png';
		    elseif($model == 'job_quote')
		    	return 'default_images/job_quote.png';
		    elseif($model == 'job_quote_line')
		    	return 'default_images/job_quote_line.png';
		}
	}
/***************************** App helper ********************************************/ 