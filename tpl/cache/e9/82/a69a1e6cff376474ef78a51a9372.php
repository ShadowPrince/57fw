<?php

/* mainpage.html */
class __TwigTemplate_e982a69a1e6cff376474ef78a51a9372 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE HTML>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <title></title>
</head>
<body>
    ";
        // line 8
        if (twig_template_get_attributes($this, (isset($context["req"]) ? $context["req"] : null), "user")) {
            // line 9
            echo "        Hello, ";
            echo twig_escape_filter($this->env, twig_template_get_attributes($this, twig_template_get_attributes($this, (isset($context["req"]) ? $context["req"] : null), "user"), "username"), "html", null, true);
            echo "!
        <br />
        Email: ";
            // line 11
            echo twig_escape_filter($this->env, twig_template_get_attributes($this, twig_template_get_attributes($this, (isset($context["req"]) ? $context["req"] : null), "user"), "email"), "html", null, true);
            echo ", 
        ";
            // line 12
            if (twig_template_get_attributes($this, twig_template_get_attributes($this, (isset($context["req"]) ? $context["req"] : null), "user"), "su")) {
                // line 13
                echo "            superuser.
        ";
            } else {
                // line 15
                echo "            user. 
        ";
            }
            // line 17
            echo "    ";
        } else {
            // line 18
            echo "        Hello, stranger!
    ";
        }
        // line 20
        echo "</body>
</html>
";
    }

    public function getTemplateName()
    {
        return "mainpage.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  57 => 20,  53 => 18,  50 => 17,  46 => 15,  42 => 13,  40 => 12,  36 => 11,  30 => 9,  28 => 8,  19 => 1,);
    }
}
