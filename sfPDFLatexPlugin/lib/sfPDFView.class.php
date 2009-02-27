<?php

/**
 * Latex PDF View class for symfony
 * @copyright 2007 Konrad Riedel
 * based on work by Georg Gell
 */
$e = error_reporting(0);
error_reporting($e);

class sfPDFView extends sfPHPView {
    private static $mapLoaded = false;
    protected static $helpers;
    protected $file;

        /**
     * sfPDFView::initialize()
     * This method is used instead of sfPHPView::initialze
     *
     * @param mixed $context
     * @param mixed $moduleName
     * @param mixed $actionName
     * @param mixed $viewName
     * @return
     **/
    public function initialize($context, $moduleName, $actionName, $viewName)
    {
    	if (sfConfig::get('sf_logging_enabled')) {
	    	$context->getLogger()->info('{sfPDFView} is used for rendering');
    	}
    	parent::initialize($context, $moduleName, $actionName, $viewName);
    	
    }
    
    public function configure()
    {
        //$this->extension = '.tex';
        //partials: only .php
        if (sfConfig::get('sf_logging_enabled')) $this->getContext()->getLogger()->info('{sfPDFView} is using'.$this->extension);
        parent::configure();
    }

    /**
     * Render the presentation.
     *
     * When the controller render mode is sfView::RENDER_CLIENT, this method will
     * render the presentation directly to the client and null will be returned.
     *
     * @return string A string representing the rendered presentation, if
     *                the controller render mode is sfView::RENDER_VAR, otherwise null.
     */
    public function render($templateVars = null)
    {
        $template         = $this->getDirectory().'/'.$this->getTemplate();
        $actionStackEntry = $this->getContext()->getActionStack()->getLastEntry();
        $actionInstance   = $actionStackEntry->getActionInstance();

        $moduleName = $actionInstance->getModuleName();
        $actionName = $actionInstance->getActionName();

        $retval = null;
        $context = $this->getContext();
        //exception, if template is missing
        $this->preRenderCheck();

        // get the render mode
        $mode = $context->getController()->getRenderMode();

        // template variables
        if ($templateVars === null)
        {
            $actionStackEntry = $context->getActionStack()->getLastEntry();
            $actionInstance   = $actionStackEntry->getActionInstance();
            $templateVars     = $actionInstance->getVarHolder()->getAll();
        }

        // assigns some variables to the template
        $this->attributeHolder->add($this->getGlobalVars());
        $this->attributeHolder->add(array('dir' => $this->getDirectory()));
        $this->attributeHolder->add($retval !== null ? $vars : $templateVars);

        $retval = null;
        try {
            $retval = $this->renderFile($template);
        }
        catch (Exception $e)
        {
            $context->getResponse()->addHttpMeta('Content-Disposition', '');
            //fixme
            return parent::render();
        }
        if ($mode == sfView::RENDER_CLIENT)
		{
			//TODO: use cache instead of /tmp
            if ($sf_logging_active = sfConfig::get('sf_logging_enabled'))
            {
                $context->getLogger()->info('{sfPDFView} render to client "'.$template.'"');
                $tempfile = "/tmp/texDebug";
                exec ("rm $tempfile*");
            } else {
                $tempfile = tempnam("/tmp","tex");
            }
            $fp  = fopen ($tempfile.'.tex',"w");
            if (!$fp) {
                die ("Could not open ".$tempfile);
            }
            fwrite($fp, $retval);
            fclose($fp);

			ob_start();
			$dir = $this->getDirectory();
            passthru("cd $dir; pdflatex -output-directory /tmp -interaction nonstopmode $tempfile.tex 2>&1");

            if (file_exists($tempfile.'.log')) {
                $log = file_get_contents("$tempfile.log");
                if (strpos($log, 'Fatal error')) {
                    unlink($tempfile.'.pdf');
                // rerun, if Bookmarksfile (.out) exists
                } elseif (file_exists($tempfile.'.out') || strpos($log, 'Rerun LaTeX')) {
                    passthru("cd /tmp; pdflatex -interaction nonstopmode $tempfile.tex 2>&1");
                }
            }
            $err = '<pre>'.ob_get_contents().'</pre>';
            ob_end_clean();

             if (file_exists($tempfile.'.pdf')) {
                $context->getResponse()->setContentType('application/pdf');
                $context->getResponse()->addHttpMeta('cache-control', 'no-cache');
                $context->getResponse()->addHttpMeta('Expires', gmdate("D, d M Y H:i:s") . " GMT",time());
                $context->getResponse()->setContent(file_get_contents("$tempfile.pdf"));
                if (!$sf_logging_active) {
                    unlink($tempfile);
                    exec ("rm $tempfile*");
                }
             } else {
                if ($sf_logging_active)
                {
                    $context->getLogger()->info('{sfPDFView} mode '. $mode.' render "'.$template.'" error"'.$err.'"');
                }
                ob_start();
                readfile($tempfile.'.tex');
                $msg = '<h2>Log</h2><pre>'.$log.'</pre>';
                $msg .= '<h2>Tex-Source:</h2><pre>'.ob_get_contents().'</pre>';
                ob_end_clean();
                throw new sfRenderException($err.$msg);
                return parent::render();

            }
        }
        return $retval;
    }

    public function getFile(){
        return $this->file;
    }

}
