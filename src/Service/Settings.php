<?php

namespace App\Service;

use App\Entity\Setting;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Yaml\Yaml;

class Settings
{

    public function __construct(
        #[Autowire('%kernel.project_dir%/config/settings.yml')]
        private readonly string              $settingFile,

        private readonly RequestStack        $requestStack,
        private readonly TenantEntityManager $tenantEntityManager,

    )
    {
    }


    public function getBySlug($slug)
    {
        $settings = $this->requestStack->getSession()->get('settings');

        if (isset($settings[$slug])) {
            $settings[$slug] = $this->getFormatterArray($settings[$slug]);
        } else {
            $this->initializeSettings();
            $settings = $this->requestStack->getSession()->get('settings');

            if (isset($settings[$slug])) {
                $settings[$slug] = $this->getFormatterArray($settings[$slug]);
            } else {
                /*
                 * Warning
                 * You are here because the setting you are looking for
                 * does not exist in the database nor in the file yml
                 *
                 */
                error_log("Undefined Setting " . $slug, 0);
                $theError = $settings[$slug]; //force to error
            }
        }

        $setting_value = $settings[$slug];

        return $this->getFormatterArray($setting_value);
    }

    private function getFormatterArray($value)
    {
        $value_tmp = @unserialize($value);
        if (is_array($value_tmp) && isset($value_tmp[0][0])) {
            $value = $value_tmp;
            $fieldValue_tmp = array();
            if (is_array($value[0])) {
                foreach ($value[0] as $field) {
                    $keys = array_keys($field);
                    $fieldValue_tmp[0][$keys[0]] = $field[$keys[0]];
                }
                $value = $fieldValue_tmp;
            }

            return serialize($value);
        } else {
            return $value;
        }
    }


    public function initializeSettings()
    {
        $yaml_flat_settings = $this->getYamlSettings();

        // dd($yaml_flat_settings);
        $db_flat_settings = array();

        $settings = $this->tenantEntityManager->getRepository(Setting::class)->getAllSetting();
dd($settings);
        $settings_array = array();
        $this->all_settings = array();

        //hasshed db settings
        foreach ($settings as $setting) {
            $db_flat_settings[$setting['slug']] = $setting;
        }

        $inexistent_settings = false;
        foreach ($yaml_flat_settings as $k => $v) {
            if (array_key_exists($k, $db_flat_settings)) {
                $setting = $db_flat_settings[$k];
                if ($setting["created_at"] == $setting["updated_at"]) {
                    $value = ($setting["field_value"] == null) ? $setting["field_default_value"] : $setting["field_value"];
                } else {
                    if ($setting["field_format"] == 'boolean') {
                        $value = ($setting["field_value"] == '1') ? true : $setting["field_value"];
                    } else {
                        $value = $setting["field_value"];
                    }
                }
                $settings_array[$k] = $value;
                $this->all_settings[$k] = $setting;
            } else {
                $inexistent_settings = true;
                $default_val = $v['setting']['field_default_value'];
                if (gettype($default_val) == "array") {
                    $default_val = serialize($default_val);
                }

                /** Create the inexistent setting */

                $_setting = new Setting();
                $_setting->setSlug($k);
                $_setting->setName($v['setting']['name']);
                $_setting->setDescription($v['setting']['description']);
                $_setting->setFieldFormat($v['setting']['field_format']);

                $_field_required = null;
                if (array_key_exists('field_required', $v['setting']) && $v['setting']['field_required'] == 1) {
                    $_field_required = 1;
                }
                $_setting->setFieldRequired($_field_required);

                $_setting->setFieldDefaultValue($default_val);

                if (array_key_exists('field_available_value', $v['setting']))
                    $available_value = (gettype($v['setting']['field_available_value']) == "array") ? serialize($v['setting']['field_available_value']) : $v['setting']['field_available_value'];
                else
                    $available_value = null;

                if (array_key_exists('special_attributes', $v['setting'])) {
                    $specialAttributes = $v['setting']['special_attributes'];
                    $_setting->setSpecialAttributes($specialAttributes);
                }

                $_setting->setFieldAvailableValue($available_value);
                $_setting->setFieldValue($default_val);
                $_setting->setCreatedAt(now());
                $_setting->setUpdatedAt(now());

                $this->tenantEntityManager->persist($_setting);

                /***/
                $settings_array[$k] = $default_val;
                $this->all_settings[$k] = $v['setting'];
            }
        }
        if ($inexistent_settings) $this->tenantEntityManager->flush();//$em->flush();

//        $this->session->set('settings', $settings_array);
        $this->requestStack->getSession()->set('settings', $settings_array);

        $this->settings_array = $settings_array;

    }

    public function getYamlSettings($only_in_frontend = false, $only_in_wizard = false)
    {
        try {
            $config = Yaml::parseFile($this->settingFile);

            if ($config === null) {
                $config = array();
            }
            if (!is_array($config)) {
                throw new \Exception("Archivo de settings inválido.");
                exit;
            }
        } catch (ParseException $e) {
            throw new \Exception("Se ha producido un error al parsear los settings.");
            exit;
        }

        $last_parent = null;
        $flat_settings = array();
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($config),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $key => $val) {
            $slug = $key;
            if (gettype($val) == "array" && $key != "children") {
                $show_in_front = true;
                if (array_key_exists('available_in_frontend', $val)) {
                    if ($val['available_in_frontend'] == 0 && $only_in_frontend)
                        $show_in_front = false;
                }

                $include_row = true;
                $show_in_wizard = (array_key_exists('available_in_wizard', $val) && $val['available_in_wizard'] == 1);
                if ($only_in_wizard) {
                    if (!$show_in_wizard) {
                        $include_row = false;
                    }
                }
                if ($only_in_frontend) {
                    if (!$show_in_front) {
                        $include_row = false;
                    }
                }


                if ($include_row) {
                    $is_widget = array_key_exists('field_format', $val);
                    $val['slug'] = $slug;
                    if ($is_widget) {
                        if ($only_in_wizard) {
                            if ($show_in_wizard) {
                                $flat_settings[$slug] = array(
                                    'parent' => $last_parent,
                                    'setting' => $val
                                );
                            }
                        } else {
                            $flat_settings[$slug] = array(
                                'parent' => $last_parent,
                                'setting' => $val
                            );
                        }

                    } else {
                        $is_parent = array_key_exists('children', $val);
                        if ($is_parent) {
                            $flat_val = array(
                                'name' => $val['name'],
                                'description' => $val['description'],
                                'field_format' => '',
                                'field_available_value' => null,
                                'field_default_value' => null,
                                'available_in_frontend' => $val['available_in_frontend'],
                                'slug' => $val['slug'],

                            );
                            $flat_settings[$slug] = array(
                                'parent' => $last_parent,
                                'setting' => $flat_val
                            );
                        }
                    }
                }
                if (array_key_exists('children', $val) && $show_in_front) {
                    //$last_depth = $iterator->getDepth();
                    $last_parent = $slug;
                }

            }
        }
        return $flat_settings;
    }

    public function getSubyamlArray($config, $setting_slug)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($config),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        $root_parent = null;
        foreach ($iterator as $key => $val) {
            if ($iterator->getDepth() == 0) $root_parent = $key;
            if ($key === $setting_slug) {
                //echo "$setting_slug $key => ";
                return array('values' => $val,
                    'depth' => $iterator->getDepth(),
                    'root' => $root_parent
                );
            }
        }
    }

    public function getAllSettings()
    {
        $this->initializeSettings();
        return $this->all_settings;
    }
}