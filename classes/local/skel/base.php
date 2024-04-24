<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Provides tool_pluginskel\local\skel\base class.
 *
 * @package     tool_pluginskel
 * @subpackage  skel
 * @copyright   2016 Alexandru Elisei <alexandru.elisei@gmail.com>, David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_pluginskel\local\skel;

use tool_pluginskel\local\util\exception;

/**
 * Base class representing data that can be generated by rendering a template.
 *
 * @copyright 2016 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class base {

    /** @var string file content */
    public $content = null;

    /** @var string name of the template to use to render our data */
    protected $template = null;

    /** @var array extra template data */
    protected $data = null;

    /** @var \tool_pluginskel\local\util\manager manager used to generate the skeleton */
    protected $manager = null;

    /** @var Monolog\Logger */
    protected $logger = null;

    /**
     * Set the template to use.
     *
     * @param strig $template name relative to the skel directory and without the extension
     */
    public function set_template($template) {
        $this->template = $template;
    }

    /**
     * Set the data to be eventually rendered.
     *
     * @param array $data
     */
    public function set_data(array $data) {
        $this->data = $data;
    }

    /**
     * Set the manager reference.
     *
     * @param manager $manager
     */
    public function set_manager(\tool_pluginskel\local\util\manager $manager) {

        if (!empty($this->manager)) {
            throw new \coding_exception('Manager has been already set!');
        }

        $this->manager = $manager;
    }

    /**
     * Set the logger to use for, well, logging.
     *
     * @param \Monolog\Logger $logger
     */
    public function set_logger(\Monolog\Logger $logger): void {

        $this->logger = $logger;
    }

     /**
      * Set the given attribute flag or value.
      *
      * @param string $attribute Attribute name
      * @param mixed $value The value to assign to the attribute, defaults to bool true.
      */
    public function set_attribute(string $attribute, $value = true) {

        if (empty($this->data)) {
            throw new \coding_exception('Skeleton data not set');
        }

        $this->data['self'][$attribute] = $value;
    }

    /**
     * Render the file contents.
     *
     * @param renderer_base $renderer
     */
    public function render($renderer) {
        $this->content = $this->normalize($renderer->render($this->get_template_name(), $this->get_template_data()));
    }

    /**
     * Returns the name of the template to be used for rendering.
     *
     * @return string
     */
    protected function get_template_name() {

        if ($this->template === null) {
            throw new exception('Template not set');
        }

        return $this->template;
    }

    /**
     * Return the data for the template.
     *
     * @return array
     */
    protected function get_template_data() {
        return $this->data;
    }

    /**
     * Make final corrections of rendered file content.
     *
     * Mustache templating engine is optimised for rendering HTML so it
     * sometimes leaves running empty lines. We get rid of them here.
     *
     * @param string $content
     * @return string
     */
    protected function normalize($content) {
        return preg_replace('/^\h*\v{2,}/m', PHP_EOL, $content);
    }

    /**
     * Returns the variables needed for rendering the template.
     *
     * The $plugintype parameter is needed if the skel class is used by different plugintypes
     * (possibly with different template files), and each plugin type requires different variables.
     *
     * @param string $plugintype The plugin type, optional.
     * @return string[].
     */
    public static function get_template_variables($plugintype = null) {
        return array();
    }
}
