<?php

/**
 * Author: WPMUDEV
 */
class JobsExperts_Framework_EditableForm
{
    public $model;
    public $mapped = array();
    public $container_classname;
    public $display_text;
    public $empty_text;
    public $html_options = array();
    public $title;
    private $id;

    public function __construct($model, $display_text, $empty_text, $mapped = array(), $container_classname = '', $title = '')
    {
        $this->model = $model;
        $this->mapped = $mapped;
        $this->container_classname = $container_classname;
        $this->display_text = $display_text;
        $this->empty_text = $empty_text;
        $this->id = uniqid();
        $this->title = $title;
    }

    public function render()
    {
        ob_start();
        //determine display empty text or display text
        $display_text = $this->display_text;
        $is_display = false;
        foreach ($this->mapped as $key => $val) {
            if ($this->model->$key) {
                $is_display = true;
                if ($val['type'] == 'dropdown') {
                    if (!is_array($this->model->$key)) {
                        $this->model->$key = array($this->model->$key);
                    }
                    if (!empty($this->model->$key)) {
                        foreach ($this->model->$key as $k => $v) {
                            if (isset($val['data'][$v])) {
                                $text[] = $val['data'][$v];
                            }
                        }
                    }
                    $display_text = str_replace('{{' . $key . '}}', implode(',', $text), $display_text);
                } else {
                    $display_text = str_replace('{{' . $key . '}}', $this->model->$key, $display_text);
                }
            }
        }
        ?>
        <div id="<?php echo $this->id ?>">
            <div class="editable_content">
                <?php
                if ($is_display) {
                    echo $display_text;
                } else {
                    echo $this->empty_text;
                }
                ?>
            </div>
            <?php
            //we display the model property for binding
            foreach ($this->mapped as $key => $val) {
                echo JobsExperts_Framework_Form::hiddenField($this->_buildFormElementName($this->model, $key), is_array($this->model->$key) ? implode(',', $this->model->$key) : esc_attr($this->model->$key));
            }
            ?>
        </div>
        <?php
        //output js script
        echo $this->_build_script();

        return ob_get_clean();
    }

    private function _build_dialog_form()
    {
        ob_start();
        ?>
        <div style="margin-top: 0">
            <form method="post" class="form_<?php echo $this->id ?> hn-editable">
                <?php foreach ($this->mapped as $key => $val): ?>
                    <?php $class = 'col-md-12'; ?>
                    <div class="row">
                        <?php if (isset($val['label'])): ?>
                            <?php $class = 'col-md-9' ?>
                            <div class="col-md-3">
                                <label><?php echo $val['label'] ?></label>
                            </div>
                        <?php endif; ?>
                        <div class="<?php echo $class ?>">
                            <?php
                            $class = isset($val['class']) ? $val['class'] : "";
                            $op = '';
                            switch ($val['type']) {
                                case 'countryDropdown':
                                    $op = JobsExperts_Framework_Form::countryDropdown($val['id'], array($this->model->$key), array(
                                        'id' => $val['id'], 'data-element' => $this->_buildFormElementName($this->model, $key),
                                        'class' => $class,
                                    ));
                                    break;
                                case 'dropdown':
                                    $op = JobsExperts_Framework_Form::dropDownList($val['id'], $this->model->$key, $val['data'], array(
                                        'id' => $val['id'], 'data-element' => $this->_buildFormElementName($this->model, $key),
                                        'class' => $class,
                                    ));
                                    break;
                                case 'textArea':
                                    $op = JobsExperts_Framework_Form::textArea($val['id'], esc_attr(wpautop($this->model->$key)), array(
                                        'rows' => 5, 'cols' => 40,
                                        'class' => $class,
                                        'data-element' => $this->_buildFormElementName($this->model, $key),
                                        'id' => $val['id']
                                    ));
                                    break;
                                case 'tags':
                                    $op = JobsExperts_Framework_Form::textField($val['id'], esc_attr($this->model->$key), array(
                                        'id' => $val['id'], 'data-element' => $this->_buildFormElementName($this->model, $key),
                                        'class' => $class,
                                        'data-type' => 'select2',
                                        'data-list' => esc_attr($val['data'])
                                    ));
                                    break;
                                case 'calendar':
                                    ?>
                                    <div data-id="<?php echo $val['id'] ?>" class="calendar <?php echo $class ?>"
                                         data-type="calendar"
                                         data-element="<?php echo $this->_buildFormElementName($this->model, $key) ?>">

                                    </div>
                                    <?php
                                    $op = JobsExperts_Framework_Form::hiddenField($val['id'], esc_attr($this->model->$key), array(
                                        'id' => $val['id'], 'data-element' => $this->_buildFormElementName($this->model, $key),
                                        'class' => $class
                                    ));
                                    break;
                                default:
                                    $op = JobsExperts_Framework_Form::$val['type']($val['id'], esc_attr($this->model->$key), array(
                                        'id' => $val['id'], 'data-element' => $this->_buildFormElementName($this->model, $key),
                                        'class' => $class,
                                    ));
                                    break;
                            }
                            echo apply_filters('jbp_expert_form_element', $op, $val, $this->model);
                            ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                <?php endforeach; ?>
                <div class="actions">
                    <button type="submit"
                            class="btn-post btn btn-primary btn-xs"><?php _e('Save', JBP_TEXT_DOMAIN) ?></button>
                    &nbsp;
                    <button type="button"
                            class="btn btn-default popover-close btn-xs"><?php _e('Cancel', JBP_TEXT_DOMAIN) ?></button>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    private function query($index, $key, $default = '')
    {
        if (isset($this->mapped[$index][$key])) {
            return $this->mapped[$index][$key];
        }
        return $default;
    }

    private function _build_script()
    {
        $form_id = 'form_' . $this->id;
        ob_start();
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var data = [];//use for store the editable data
                //parent
                var container = $('#<?php echo $this->id ?>');
                //enable validate
                var popover_element = container.find('.editable_content');
                //load popover
                popover_element.popover({
                    content: '<?php echo preg_replace('/^\s+|\n|\r|\s+$/m', '', $this->_build_dialog_form()); ?>',
                    html: true,
                    trigger: 'click',
                    container: '.hn-container',
                    placement: 'auto',
                    title: '<?php echo $this->title ?>'
                }).on('shown.bs.popover', function () {
                    //init the form
                    var that = $(this);
                    var next = that.next();
                    var form = $('.<?php echo $form_id ?>');
                    form.validationEngine('attach', {
                        binded: false,
                        scroll: false
                    });
                    $.each(data, function (i, v) {
                        form.find(':input[name="' + v.name + '"]').val(v.value);
                    })

                    if (form.find('[data-type="select2"]').size() > 0) {
                        form.find('[data-type="select2"]').each(function () {
                            $(this).select2({
                                tags: $(this).data('list'),
                                placeholder: "<?php esc_attr_e('Add a tag, use commas to separate'); ?>",
                                tokenSeparators: [","]
                            })
                        })
                    }

                    if (form.find('[data-type="calendar"]').size() > 0) {
                        form.find('[data-type="calendar"]').each(function () {
                            $(this).datepicker({
                                startDate: "today",
                                todayHighlight: true,
                                format: "M d, yyyy"
                            }).on('changeDate', function (e) {
                                var id = $(this).data('id');
                                $('#' + id).val(e.format());
                                /*var element_name = $(this).data('element');
                                 $('input[name="' + element_name + '"]').val(e.format());*/
                            })
                        })
                    }

                    //hook
                    form.on('submit', function () {
                        //we will need to update the display_text or fallback to empty_text
                        //first check does this form have enought data
                        var form = $(this);
                        if (form.validationEngine('validate')) {
                            var display_text = '<?php echo addslashes($this->display_text) ?>';
                            var empty_text = '<?php echo addslashes($this->empty_text )?>';
                            var is_null = true;
                            $.each($(this).serializeArray(), function (i, v) {
                                //get current id
                                var current = v.name;
                                //get element name
                                var element_name = form.find('#' + current).data('element');
                                //now bind the data to element
                                $('input[name="' + element_name + '"]').val(v.value);
                                //next we need to update the element text
                                if (form.find('#' + current).is('select')) {
                                    //select, replace the display
                                    display_text = display_text.replace('{{' + v.name + '}}', form.find('#' + current + ' option:selected').text());
                                } else {
                                    display_text = display_text.replace('{{' + v.name + '}}', v.value);
                                }
                                if ($.trim(v.value.length) > 0) {
                                    is_null = false;
                                }
                            });
                            if (is_null == false) {
                                container.find('.editable_content').html(display_text);
                            } else {
                                container.find('.editable_content').html(empty_text);
                            }
                            data = $(this).serializeArray();
                            hide_popover();
                        }
                        return false;
                    });

                });

                function hide_popover() {
                    popover_element.popover('hide');
                }

                $('html').on('mouseup', function (e) {
                    if (!$(e.target).closest('.popover').length) {
                        $('.popover').each(function () {
                            $(this.previousSibling).popover('hide');
                        });
                    }
                    //check does this target is cancel
                    if ($(e.target).hasClass('popover-dismiss')) {
                        $(this.previousSibling).popover('hide');
                    }
                });
                $('body').on('click', '.popover-close', function () {
                    popover_element.popover('hide');
                })
            })

        </script>
        <?php
        $script = ob_get_clean();

        return $script;

    }

    /**
     * @param $model
     * @param $attribute
     *
     * @return string
     */
    private function _buildFormElementName($model, $attribute)
    {
        $model_class_name = get_class($model);
        $frm_element_name = $model_class_name . "[$attribute]";

        return $frm_element_name;
    }
}