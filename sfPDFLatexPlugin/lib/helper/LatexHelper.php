<?php

	function e($text)
    {
		$in = array('/"/',
		            '/&/',
		            '/%/',
		            '/\$/',
		            '/@/',
		            '/#/',
		            '/\^/',
		            '/\[/',
		            '/\]/',
		            '/\x80/',
				    '/\n/');
		$out = array('\\symbol{34}',
		            '\\symbol{38}',
		            '\\symbol{37}',
		            '\\symbol{36}',
		            '\\symbol{64}',
		            '\\symbol{35}',
		            '\\symbol{94}',
		            '\\texttt{\\symbol{91}}',
		            '\\texttt{\\symbol{93}}',
		            '\\euro{}',
				    '');
		
		return preg_replace($in, $out, $text);
	}

	function with_path($file)
	{
		$dir = sfConfig::get('sf_app_module_dir').'/'.sfContext::getInstance()->getModuleName().'/'.sfConfig::get('sf_app_module_template_dir_name');
		return "$dir/$file";
	}
?>
