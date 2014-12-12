<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Defines the PHP sub-compiler
 */
namespace RDev\Views\Compilers\SubCompilers;
use RDev\Views;

class PHP extends SubCompiler
{
    /**
     * {@inheritdoc}
     */
    public function compile(Views\ITemplate $template, $content)
    {
        // Create local variables for use in eval()
        extract($template->getVars());

        $startOBLevel = ob_get_level();
        ob_start();

        // Compile the functions
        $templateFunctions = $this->parentCompiler->getTemplateFunctions();

        foreach($templateFunctions as $functionName => $callback)
        {
            $regex = "/%s\s*%s\(\s*((?:(?!\)\s*%s).)*)\s*\)\s*%s/";
            $functionCallString = 'call_user_func_array($templateFunctions["' . $functionName . '"], [\1])';
            // Replace function calls in escaped tags
            $content = preg_replace(
                sprintf(
                    $regex,
                    preg_quote($template->getEscapedOpenTag(), "/"),
                    preg_quote($functionName, "/"),
                    preg_quote($template->getEscapedCloseTag(), "/"),
                    preg_quote($template->getEscapedCloseTag(), "/")),
                '<?php echo $this->parentCompiler->getXSSFilter()->run(' . $functionCallString . '); ?>',
                $content
            );
            // Replace function calls in unescaped tags
            $content = preg_replace(
                sprintf(
                    $regex,
                    preg_quote($template->getUnescapedOpenTag(), "/"),
                    preg_quote($functionName, "/"),
                    preg_quote($template->getUnescapedCloseTag(), "/"),
                    preg_quote($template->getUnescapedCloseTag(), "/")),
                "<?php echo $functionCallString; ?>",
                $content
            );
        }

        // Notice the little hack inside eval() to compile inline PHP
        if(eval("?>" . $content) === false)
        {
            // Prevent an invalid template from displaying
            while(ob_get_level() > $startOBLevel)
            {
                ob_end_clean();
            }

            throw new \RuntimeException("Invalid PHP inside template");
        }

        return ob_get_clean();
    }
}