<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008, 2012 Filip Procházka (filip.prochazka@kdyby.org)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Kdyby\Extension\Redis\Diagnostics;

use Kdyby;
use Kdyby\Extension\Redis\RedisClientException;
use Nette;
use Nette\Diagnostics\Debugger;



/**
 * @author Filip Procházka <filip.prochazka@kdyby.org>
 */
class Panel extends Nette\Object implements Nette\Diagnostics\IBarPanel
{

	/**
	 * @var int
	 */
	private $totalTime = 0;



	/**
	 */
	public function begin()
	{
		Debugger::timer('redis-client-timer');
	}



	/**
	 */
	public function end()
	{
		$this->totalTime += Debugger::timer('redis-client-timer');
	}



	/**
	 * Renders HTML code for custom tab.
	 * @return string
	 */
	public function getTab()
	{
		return '<span title="Redis Storage">' .
			'<img alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAAuIwAALiMBeKU/dgAAAAd0SU1FB9wHCxYIODR5wLEAAAMUSURBVDjLPZO7bxxVGMV/3517Z2Z37bU39mLiBCURwcRBRIIEwqugp4GCBkUREo+CDqUIFClpgIII8RckBUEoEg1lQKIkiQRIeUgUedlO7PUjXts7cx/z0Tic6qdzpHOqIwC3jh7kyI077LIARlVflCw7i8g7wFWN8WsRuQKEIzfuKLuSJ3D7pedNHAymJHcvi3VfSJa93XnjTVrHXyUsPGDj50uIyN9G5Csj8ltomvX5m3ej3V093GwN38oPHPjUjHdfR5VmOCQ/PKfdd9+X4a+/kE1MUG1sHNtO6VLb2tulMT/cnD9wRW4fPXhGVU/j3LHZb85TzM1Dk3Tn2p+y+PlntE68ws7162wWBRsxcnVjTZ/rjMkL411KY27Z+1X1bd852llGWn5E6IxBShIW7iPOkZaXaR8/wfCfvyiM4WCrLVN5rlFVlur6iFzYO6ktm2kvszIz1qYz3Sf6QPvka/Q+/ATbn+HhubPs/PE7ajNiozyOkZXa61aMkn081Z2uUnPycYys1Z5quEnbe3SwQnhwD20aNi//hKbEwAfujkYMfCA0KrOtMsjVuf11O8vyh3XNUlXTqJKJMFMU7G2VOGtZG424P6oYpQZQ+nnOvlaJgsqF2V7Tz3PZVxYYEZaqihUfiE1DJkJmDD4ljAiTzrK/LLHG8KiqWaprbC5yZ+D9oRXvmclzni4LnioKlmvPagg0qnSdZV+rRcsYBrVnoapIqpSZwR4e66xuxXhozQeWvWe5rpkpS6ZzR7/Iiao4MTwOgX+rbXzT0LEZPefYk+fI5WemdKYotMwyRinJug+sh4ARoZ/n5MawFgLDEOk6yx7nGHeZ+gZW6lrsVkzXtuPO8XFrmXROZ8tS+kXOqg88rGuiKhPW8mynTdtmVCnp4qiWzRhJqvfk4mxvTlXfUzhjRPqtLGPSWe1ZJwpEbSiNYTMlXfVedmIiqnqB7wR+FICLsz0HTKvqB8CXRmTKirAnd1oYIwMftEpJkirA9yJyHlg4tbhe/f/G3SKDalfhI+CciEw8yVT14q734NTienzi/weR/4QEsuMkfwAAAABJRU5ErkJggg==" />' .
			sprintf('%0.1f', $this->totalTime * 1000) . ' ms' .
			'</span>';
	}



	/**
	 * Renders HTML code for custom panel.
	 * @return string
	 */
	public function getPanel()
	{
	}



	/**
	 * @param \Exception|RedisClientException $e
	 *
	 * @return array
	 */
	public static function renderException($e)
	{
		if ($e instanceof RedisClientException && $e->data) {
			$maxLen = Debugger::$maxLen;
			Debugger::$maxLen = 0;
			$panel = array(
				'tab' => 'Redis Response',
				'panel' => '<h3>Redis Response (' . strlen($e->data) . ')</h3>' .
					'<pre class="nette-dump"><span class="php-string">' .
					Nette\Templating\Helpers::escapeHtml($e->data) .
					'</span></pre>'
			);
			Debugger::$maxLen = $maxLen;
			return $panel;
		}
	}



	/**
	 * @return \Kdyby\Extension\Redis\Diagnostics\Panel
	 */
	public static function register()
	{
		Debugger::$blueScreen->addPanel(array($panel = new static(), 'renderException'));
		Debugger::$bar->addPanel($panel);
		return $panel;
	}

}