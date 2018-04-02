<?php

namespace BadChoice\Reports\DataTransformers\Transformers;

use BadChoice\Reports\DataTransformers\TransformsRowInterface;

class Link implements TransformsRowInterface
{
    public function transformRow($field, $row, $value, $transformData)
    {
        $class = "";
        $text  = $value;
        $link  = $this->parseLink($row, $transformData);
        if (is_array($transformData)) {
            $class = $transformData['class'] ?? "";
            if (isset($transformData['icon'])) {
                return "<a class='{$class}' href='" . url($link) . "' style='font-size:15px'> " . icon($transformData['icon']) . "</a>";
            }
            $text = $this->getDisplayText($transformData, $value);
        }
        return "<a class='{$class}' href='" . url($link) . "'>{$text}</a>";
    }

    /**
     * @param $row
     * @param $link
     * @return mixed
     */
    public function parseLink($row, $link)
    {
        $link    = is_array($link) ? $link['url'] : $link;
        $matches = null;
        $results = preg_match_all("/{([|,a-z,A-Z,0-9,_,-,\.]*)}/", $link, $matches);
        if (! $results) {
            return $link;
        }
        foreach (range(0, $results - 1) as $i) {
            $options = explode('||', substr($matches[0][$i], 1, -1));
            $value = collect($options)->first(function ($option) use ($row) {
                return data_get($row, $option);
            });
            $link   = str_replace($matches[0][$i], data_get($row, $value), $link);
        }
        return $link;
    }

    protected function getDisplayText($transformData, $value)
    {
        if (isset($transformData['textCallback'])) {
            return $transformData['textCallback']($value);
        }
        return ($transformData['content'] ?? "") . ($transformData['text'] ?? $value);
    }
}
