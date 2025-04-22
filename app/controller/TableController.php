<?php

namespace App\Controller;

use \App\Controller\Controller;
use App\Model\Subtopics;
use App\Model\Topics;

class TableController extends Controller
{
    public function index($variables)
    {
        $list = [];

        $topics = new Topics();
        $subtopics = new Subtopics();

        $listOfTopics = $topics->select(["topics.id as topicId", "st.id as subtopicId", "name", "sub_name"])->join("subtopics as st", "st.topic_id", "=", "topics.id")->get();
        $textForTopic = $subtopics->select(["id", "text"])->where("id", "=", $variables->subtopicId ?? 0)->first();

        $selectedSubtopicId = (int) ($textForTopic->id ?? 0);
        $subtopicText = (string) ($textForTopic->text ?? "");

        foreach ($listOfTopics as $topic) {
            $topicId = (int) ($topic->topicId ?? 0);
            $subtopicId = (int) ($topic->subtopicId ?? 0);
            $name = (string) ($topic->name ?? "");
            $subName = (string) ($topic->sub_name ?? "");

            if ($topicId && $subtopicId && strlen($name) && strlen($subName)) {
                $list[$topicId]["name"] = $name;

                $list[$topicId]["list"][$subtopicId] = [
                    "subname" => $subName,
                    "text" => $selectedSubtopicId === $subtopicId ? $subtopicText : ""
                ];
            }
        }

        $this->view("table", ["list" => $list]);
    }
}