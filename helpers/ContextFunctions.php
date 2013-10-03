<?php

function sites_set_current_context($context)
{
    $contextType = get_class($context);
    get_view()->$contextType = $context;
}

function sites_get_current_context($contextType = null)
{
    //assume the context type has been set in the loop
    if(!$contextType) {
        $contextType = sites_get_current_context_type();
    }
    return get_view()->$contextType;
}

function sites_get_contexts_for_loop($contextType)
{
    //not bothering with Omeka_Inflector because all the sites contexts just add 's'
    //see the controller showAction
    $pluralized = $contextType . 's';
    return get_view()->$pluralized;
}

function sites_get_current_context_type()
{
    return get_view()->currentContextType;
}

function sites_loop_contexts($contextType)
{
    $pluralized = $contextType . 's';
    get_view()->currentContextType = $contextType;
    return loop_records($pluralized, sites_get_contexts_for_loop($contextType), 'sites_set_current_context');
}

function sites_set_contexts_for_loop($contexts)
{
    $contextType = get_class($contexts[0]);
    $pluralized = $contextType . 's';
    get_view()->$pluralized = $contexts;
}

function sites_link_to_original_context($context = null, $text = null)
{
    if(!$context) {
        $context = sites_get_current_context();
    }
    if(!$context) {
        return;
    }
    if(!$text) {
        $text = sites_context('title');
    }

    return "<a href='{$context->url}'>$text</a>";
}

function sites_context($column, $options=array(), $context = null)
{
    if(!$context) {
        $context = sites_get_current_context();
    }
    if(!$context) {
        return;
    }
    $returnText = html_escape($context->$column);

    if (isset($options['snippet'])) {
        $returnText = snippet($returnText, 0, (int)$options['snippet']);
    }

    return $returnText;

}