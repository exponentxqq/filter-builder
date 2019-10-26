<?php

namespace Exper\Tests;

use Exper\FilterBuilder\Helpers;
use Illuminate\Support\Facades\Config;

class HelpersTest extends BaseCase
{
    public function testFormatField()
    {
        $result = Helpers::formatField('question_id', 'question_items');
        $expect = 'QuestionItemsQuestionId';
        $this->assertEquals($expect, $result);

        $result = Helpers::formatField('question_items.id', 'questions');
        $expect = 'QuestionItemsId';
        $this->assertEquals($expect, $result);
    }
}
