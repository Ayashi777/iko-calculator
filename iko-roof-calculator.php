<?php
/**
 * Plugin Name: IKO Калкулатор за покрив
 * Description: Предоставя кратък код за калкулатор на покрив, използващ ACF.
 * Version: 1.0.4
 */

define('IKO_CALCULATOR_URL', plugin_dir_url(__FILE__));

//add_action('wp_enqueue_scripts', function() {
//    wp_enqueue_style('iko-roof-calculator-style', IKO_CALCULATOR_URL . 'style.css', [], '1.0.0');
//});

// 1. Добавяме страница с настройки на ACF
if(function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title'    => 'Настройки на IKO Калкулатор за нов покрив',
        'menu_title'    => 'IKO Калкулатор',
        'menu_slug'     => 'iko-new-calculator-settings',
        'capability'    => 'manage_options',
        'icon_url'      => 'dashicons-calculator',
        'redirect'      => false
    ));
}

// 1. Регистрация на полета на ACF
add_action('acf/init', 'register_iko_calculator_fields');
function register_iko_calculator_fields() {
    if (!function_exists('acf_add_local_field_group')) return;

    // Група полета за колекции керемиди
    acf_add_local_field_group(array(
        'key' => 'group_iko_shingles',
        'title' => 'Колекции битумни керемиди IKO',
        'fields' => array(
            array(
                'key' => 'field_iko_shingles',
                'label' => 'Колекции керемиди',
                'name' => 'iko_shingles',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Добави колекция',
                'sub_fields' => array(
                    array(
                        'key' => 'field_shingle_name',
                        'label' => 'Име на колекцията',
                        'name' => 'name',
                        'type' => 'text',
                        'required' => 1
                    ),
                    array(
                        'key' => 'field_main_coef',
                        'label' => 'Коефициент (основа)',
                        'name' => 'main_coef',
                        'type' => 'number',
                        'step' => '0.01',
                        'required' => 1
                    ),
                    array(
                        'key' => 'field_ridge_coef',
                        'label' => 'Коефициент (било)',
                        'name' => 'ridge_coef',
                        'type' => 'number',
                        'step' => '0.1'
                    )
                )
            )
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'iko-new-calculator-settings',
                ),
            ),
        ),
    ));

    // Група полета за стартови материали
    acf_add_local_field_group(array(
        'key' => 'group_iko_starters',
        'title' => 'Стартови материали IKO',
        'fields' => array(
            array(
                'key' => 'field_iko_starters',
                'label' => 'Стартови материали',
                'name' => 'iko_starters',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Добави материал',
                'sub_fields' => array(
                    array(
                        'key' => 'field_starter_name',
                        'label' => 'Име',
                        'name' => 'name',
                        'type' => 'text',
                        'required' => 1
                    ),
                    array(
                        'key' => 'field_starter_coef',
                        'label' => 'Коефициент',
                        'name' => 'coef',
                        'type' => 'number',
                        'step' => '0.1',
                        'required' => 1
                    )
                )
            )
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'iko-new-calculator-settings',
                ),
            ),
        ),
    ));

    // Група полета за материали за билото
    acf_add_local_field_group(array(
        'key' => 'group_iko_ridge_mats',
        'title' => 'Материали за билото IKO',
        'fields' => array(
            array(
                'key' => 'field_iko_ridge_mats',
                'label' => 'Материали за билото',
                'name' => 'iko_ridge_mats',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Добави материал',
                'sub_fields' => array(
                    array(
                        'key' => 'field_ridge_mat_name',
                        'label' => 'Име',
                        'name' => 'name',
                        'type' => 'text',
                        'required' => 1
                    ),
                    array(
                        'key' => 'field_ridge_mat_coef',
                        'label' => 'Коефициент',
                        'name' => 'coef',
                        'type' => 'number',
                        'step' => '0.1',
                        'required' => 1
                    )
                )
            )
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'iko-new-calculator-settings',
                ),
            ),
        ),
    ));

    // Група полета за подложни килими
    acf_add_local_field_group(array(
        'key' => 'group_iko_underlays',
        'title' => 'Подложни материали IKO',
        'fields' => array(
            array(
                'key' => 'field_iko_underlays',
                'label' => 'Подложни килими',
                'name' => 'iko_underlays',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Добави килим',
                'sub_fields' => array(
                    array(
                        'key' => 'field_underlay_name',
                        'label' => 'Име',
                        'name' => 'name',
                        'type' => 'text',
                        'required' => 1
                    ),
                    array(
                        'key' => 'field_underlay_coef_high',
                        'label' => 'Коеф. за стръмни покриви',
                        'name' => 'coef_high',
                        'type' => 'number',
                        'step' => '0.1',
                        'required' => 1
                    ),
                    array(
                        'key' => 'field_underlay_coef_low',
                        'label' => 'Коеф. за полегати покриви',
                        'name' => 'coef_low',
                        'type' => 'number',
                        'step' => '0.1',
                        'required' => 1
                    )
                )
            )
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'iko-new-calculator-settings',
                ),
            ),
        ),
    ));
}

// 2. Добавяне на страница с настройки
add_action('admin_menu', 'iko_add_admin_page');
function iko_add_admin_page() {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(array(
            'page_title' => 'Настройки на IKO Калкулатор за покрив',
            'menu_title' => 'IKO Калкулатор',
            'menu_slug' => 'iko-new-calculator-settings',
            'capability' => 'manage_options',
            'icon_url' => 'dashicons-calculator',
            'redirect' => false
        ));
    }
}

// 3. Импорт на стандартни данни
function iko_import_default_data() {
    // Колекции керемиди
    $shingles = array(
        array('name' => 'Cambridge Xtreme 9,5°', 'main_coef' => 2.17, 'ridge_coef' => 0),
        array('name' => 'Cambridge Xpress', 'main_coef' => 2.17, 'ridge_coef' => 0),
        array('name' => 'Monarch', 'main_coef' => 2.58, 'ridge_coef' => 10.3),
        array('name' => 'Monarch – Diamant', 'main_coef' => 2.48, 'ridge_coef' => 7.4),
        array('name' => 'ArmourShield PLUS', 'main_coef' => 2, 'ridge_coef' => 6),
        array('name' => 'DiamantShield PLUS', 'main_coef' => 2, 'ridge_coef' => 6),
        array('name' => 'Armourglass PLUS', 'main_coef' => 2, 'ridge_coef' => 0),
        array('name' => 'Victorian PLUS', 'main_coef' => 2, 'ridge_coef' => 0),
        array('name' => 'Diamant PLUS', 'main_coef' => 2, 'ridge_coef' => 6),
        array('name' => 'Superglass - 3T', 'main_coef' => 2, 'ridge_coef' => 6),
        array('name' => 'Superglass – Biber', 'main_coef' => 2, 'ridge_coef' => 0),
        array('name' => 'Superglass – Hex', 'main_coef' => 3, 'ridge_coef' => 8.9)
    );
    update_field('field_iko_shingles', $shingles, 'option');

    // Стартови материали
    $starters = array(
        array('name' => 'Superglass - 3T', 'coef' => 14),
        array('name' => 'Starter Strip', 'coef' => 21),
        array('name' => 'Monarch', 'coef' => 10.3),
        array('name' => 'Monarch – Diamant', 'coef' => 7.4),
        array('name' => 'ArmourShield PLUS', 'coef' => 6),
        array('name' => 'DiamantShield PLUS', 'coef' => 6),
        array('name' => 'Armourglass PLUS', 'coef' => 8),
        array('name' => 'Victorian PLUS', 'coef' => 2),
        array('name' => 'Diamant PLUS', 'coef' => 6),
        array('name' => 'Superglass – Biber', 'coef' => 2),
        array('name' => 'Superglass – Hex', 'coef' => 8.9)
    );
    update_field('field_iko_starters', $starters, 'option');

    // Материали за билото
    $ridge_mats = array(
        array('name' => 'Superglass - 3T', 'coef' => 6),
        array('name' => 'Superglass – Hex', 'coef' => 8.9),
        array('name' => 'Monarch', 'coef' => 10.3),
        array('name' => 'Monarch – Diamant', 'coef' => 7.4),
        array('name' => 'ArmourShield PLUS', 'coef' => 6),
        array('name' => 'DiamantShield PLUS', 'coef' => 6),
        array('name' => 'Armourglass PLUS', 'coef' => 8),
        array('name' => 'Diamant PLUS', 'coef' => 6),
        array('name' => 'Superglass – Biber', 'coef' => 2)
    );
    update_field('field_iko_ridge_mats', $ridge_mats, 'option');

    // Подложни килими
    $underlays = array(
        array('name' => 'GO', 'coef_high' => 30, 'coef_low' => 15),
        array('name' => 'Pro', 'coef_high' => 30, 'coef_low' => 15),
        array('name' => 'Pro Plus', 'coef_high' => 30, 'coef_low' => 15)
    );
    update_field('field_iko_underlays', $underlays, 'option');
}

// 4. Хук за активиране и импорт на данни
register_activation_hook(__FILE__, 'iko_calculator_activate');
function iko_calculator_activate() {
    // Проверка дали данните вече са импортирани
    if (!get_field('iko_shingles', 'option')) {
        iko_import_default_data();
    }
}

// 5. Допълнителни функции за управление на данните
add_action('admin_init', 'iko_add_data_management');
function iko_add_data_management() {
    // Бутон за принудителен импорт на данни
    if (isset($_GET['force_iko_import']) && current_user_can('manage_options')) {
        iko_import_default_data();
        wp_redirect(admin_url('admin.php?page=iko-new-calculator-settings'));
        exit;
    }

    // Добавяне на бутон в администрацията
    add_action('admin_notices', function() {
        echo '<div class="notice notice-info">';
        echo '<p>За принудителен импорт на данните на калкулатора: <a href="'.admin_url('?force_iko_import=1').'" class="button">Импортирай данни</a></p>';
        echo '</div>';
    });
}

// Шорткод для калькулятора
add_shortcode('iko_new_roof_calculator', 'iko_new_calculator_shortcode');
function iko_new_calculator_shortcode() {
    ob_start();
    ?>
    <div class="calc-section">

        <div class="iko-new-calculator-container">
            <h1>Параметри на изчислението</h1>

            <div class="iko-new-form-group">
                <label for="iko-new-collection">Изберете колекция битумни керемиди:</label>
                <select id="iko-new-collection" required>
                    <option value="">-- Изберете --</option>
                    <?php
                    $shingles = get_field('iko_shingles', 'option');
                    if($shingles) {
                        foreach($shingles as $item) {
                            echo '<option value="'.esc_attr($item['name']).'" 
                              data-main="'.esc_attr($item['main_coef']).'" 
                              data-ridge="'.esc_attr($item['ridge_coef']).'">'
                                .esc_html($item['name']).'</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="iko-new-form-group">
                <label for="iko-new-angle">Изберете наклон:</label>
                <select id="iko-new-angle" required>
                    <option value="">-- Изберете --</option>
                    <option value="9.5-20">от 9,5° до 20°</option>
                    <option value="21-85">от 21° до 85°</option>
                </select>
            </div>

            <h2>Схема на покрив за изчисление</h2>
            <img src="<?php echo IKO_CALCULATOR_URL; ?>calc-roof.webp" alt="Схема на покрива" class="iko-calc-img">
            <div class="grid-3">
                <div class="iko-new-form-group">
                    <label for="iko-new-length">1. Дължина на стрехата (м):</label>
                    <input type="number" id="iko-new-length" min="0.1" step="0.1" value="">
                </div>

                <div class="iko-new-form-group">
                    <label for="iko-new-slope">2. Дължина на склона (м):</label>
                    <input type="number" id="iko-new-slope" min="0.1" step="0.1" value="">
                </div>

                <div class="iko-new-form-group">
                    <label for="iko-new-ridge">3. Дължина на билото (м):</label>
                    <input type="number" id="iko-new-ridge" min="0.1" step="0.1" value="">
                </div>

            </div>


            <label>Изберете материал за стартова лента</label>
            <div class="iko-new-form-group">
                <select id="iko-new-starter" required>
                    <option value="">-- Изберете --</option>
                    <?php
                    $starters = get_field('iko_starters', 'option');
                    if($starters) {
                        foreach($starters as $item) {
                            echo '<option value="'.esc_attr($item['name']).'" 
                              data-coef="'.esc_attr($item['coef']).'">'
                                .esc_html($item['name']).'</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <label>Изберете битумни керемиди за билото</label>
            <div class="iko-new-form-group">
                <select id="iko-new-ridge-material" required>
                    <option value="">-- Изберете --</option>
                    <?php
                    $ridge_mats = get_field('iko_ridge_mats', 'option');
                    if($ridge_mats) {
                        foreach($ridge_mats as $item) {
                            echo '<option value="'.esc_attr($item['name']).'" 
                              data-coef="'.esc_attr($item['coef']).'">'
                                .esc_html($item['name']).'</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <label>Изберете подложен килим</label>
            <div class="iko-new-form-group">
                <select id="iko-new-underlay" required>
                    <option value="">-- Изберете --</option>
                    <?php
                    $underlays = get_field('iko_underlays', 'option');
                    if($underlays) {
                        foreach($underlays as $item) {
                            echo '<option value="'.esc_attr($item['name']).'" 
                              data-high="'.esc_attr($item['coef_high']).'" 
                              data-low="'.esc_attr($item['coef_low']).'">'
                                .esc_html($item['name']).'</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="iko-new-form-actions">
                <button type="button" id="iko-new-reset" class="iko-new-btn">Изчисти</button>
                <button type="button" id="iko-new-calculate" class="iko-new-btn">Получете резултат</button>
            </div>
        </div>

        <div class="iko-new-results" id="iko-new-results">
            <h2>Резултат</h2>
            <table class="iko-new-results-table">
                <tbody id="iko-new-results-body">
                <tr>
                    <td class="res-calc" colspan="2" style="text-align: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-500 mx-auto" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        <span id="enter-params"> Въведете параметри за изчисление</span>
                    </td>

                </tr>
                </tbody>
            </table>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const collectionSelect = document.getElementById('iko-new-collection');
            const starterSelect = document.getElementById('iko-new-starter');
            const ridgeMaterialSelect = document.getElementById('iko-new-ridge-material');

            // Запазваме всички оригинални опции
            const allStarterOptions = Array.from(starterSelect.options);
            const allRidgeOptions = Array.from(ridgeMaterialSelect.options);

            // Определяме наличните материали за всяка колекция (с точни имена)
            const collectionMaterials = {
                'Cambridge Xtreme 9,5°': {
                    starters: ['Superglass - 3T', 'Starter Strip'],
                    ridgeMaterials: ['Superglass - 3T']
                },
                'Cambridge Xpress': {
                    starters: ['Superglass - 3T', 'Starter Strip'],
                    ridgeMaterials: ['Superglass - 3T']
                },
                'Monarch': {
                    starters: ['Superglass - 3T', 'Monarch'],
                    ridgeMaterials: ['Superglass - 3T', 'Monarch']
                },
                'Monarch – Diamant': {
                    starters: ['Superglass - 3T', 'Monarch – Diamant'],
                    ridgeMaterials: ['Superglass - 3T', 'Monarch – Diamant']
                },
                'ArmourShield PLUS': {
                    starters: ['Superglass - 3T', 'ArmourShield PLUS'],
                    ridgeMaterials: ['Superglass - 3T', 'ArmourShield PLUS']
                },
                'DiamantShield PLUS': {
                    starters: ['Superglass - 3T',  'DiamantShield PLUS'],
                    ridgeMaterials: ['Superglass - 3T', 'DiamantShield PLUS']
                },
                'Armourglass PLUS': {
                    starters: ['Superglass - 3T', 'Armourglass PLUS'],
                    ridgeMaterials: ['Superglass - 3T', 'Armourglass PLUS']
                },
                'Victorian PLUS': {
                    starters: ['Superglass - 3T', 'Victorian PLUS'],
                    ridgeMaterials: ['Superglass - 3T']
                },
                'Diamant PLUS': {
                    starters: ['Superglass - 3T', 'Diamant PLUS'],
                    ridgeMaterials: ['Superglass - 3T', 'Diamant PLUS']
                },
                'Superglass - 3T': {
                    starters: ['Superglass - 3T'],
                    ridgeMaterials: ['Superglass - 3T']
                },
                'Superglass – Biber': {
                    starters: ['Superglass - 3T', 'Superglass – Biber'],
                    ridgeMaterials: ['Superglass - 3T']
                },
                'Superglass – Hex': {
                    starters: ['Superglass - 3T', 'Superglass – Hex'],
                    ridgeMaterials: ['Superglass - 3T', 'Superglass – Hex']
                }
            };

            // Функция за конвертиране на число от запетая към точка
            function parseEuropeanNumber(numberString) {
                if (!numberString) return 0;
                // Заменяме запетаята с точка и конвертираме в число
                const number = parseFloat(numberString.toString().replace(',', '.'));
                return isNaN(number) ? 0 : number;
            }

            // Актуализираме наличните опции при промяна на колекцията
            collectionSelect.addEventListener('change', function() {
                const selectedCollection = this.value;
                updateMaterialOptions(selectedCollection);
            });

            function updateMaterialOptions(collection) {
                const materials = collectionMaterials[collection] || { starters: [], ridgeMaterials: [] };

                // Актуализираме стартовите материали
                updateSelectOptions(starterSelect, materials.starters, allStarterOptions);

                // Актуализираме материалите за билото
                updateSelectOptions(ridgeMaterialSelect, materials.ridgeMaterials, allRidgeOptions);
            }

            function updateSelectOptions(select, allowedOptions, originalOptions) {
                const currentValue = select.value;

                // Изчистваме select
                select.innerHTML = '<option value="">-- Изберете --</option>';

                // Добавяме само позволените опции
                allowedOptions.forEach(optionValue => {
                    const originalOption = originalOptions.find(opt => opt.value === optionValue);
                    if (originalOption) {
                        const newOption = document.createElement('option');
                        newOption.value = originalOption.value;
                        newOption.textContent = originalOption.textContent;
                        newOption.dataset.coef = originalOption.dataset.coef;
                        select.appendChild(newOption);
                    }
                });

                // Възстановяваме предишната стойност, ако е позволена
                if (allowedOptions.includes(currentValue)) {
                    select.value = currentValue;
                } else {
                    select.value = '';
                }
            }

            // Клик върху бутона за изчисление
            document.getElementById('iko-new-calculate').addEventListener('click', function() {
                calculateMaterials();
            });

            // Клик върху бутона за изчистване
            document.getElementById('iko-new-reset').addEventListener('click', function() {
                document.getElementById('iko-new-collection').value = '';
                document.getElementById('iko-new-angle').value = '';
                document.getElementById('iko-new-length').value = '';
                document.getElementById('iko-new-slope').value = '';
                document.getElementById('iko-new-ridge').value = '';

                // Възстановяваме всички опции за стартовите материали и билото
                starterSelect.innerHTML = '';
                allStarterOptions.forEach(option => {
                    starterSelect.appendChild(option.cloneNode(true));
                });

                ridgeMaterialSelect.innerHTML = '';
                allRidgeOptions.forEach(option => {
                    ridgeMaterialSelect.appendChild(option.cloneNode(true));
                });

                document.getElementById('iko-new-underlay').value = '';

                document.getElementById('iko-new-results-body').innerHTML = `
            <tr>
               <td class="res-calc" colspan="2" style="text-align: center;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-500 mx-auto" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
        <span> Въведете параметри за изчисление</span>
        </td>
            </tr>
        `;
            });

            function calculateMaterials() {
                // Get input values
                const length = parseFloat(document.getElementById('iko-new-length').value);
                const slope = parseFloat(document.getElementById('iko-new-slope').value);
                const ridge = parseFloat(document.getElementById('iko-new-ridge').value);
                const angle = document.getElementById('iko-new-angle').value;

                // Validate inputs
                if (!document.getElementById('iko-new-collection').value) {
                    alert('Моля, изберете колекция керемиди');
                    return;
                }

                if (!angle) {
                    alert('Моля, изберете наклон');
                    return;
                }

                if (!length || length <= 0) {
                    alert('Моля, въведете коректна дължина на стрехата');
                    return;
                }

                if (!slope || slope <= 0) {
                    alert('Моля, въведете коректна дължина на склона');
                    return;
                }

                if (!ridge || ridge <= 0) {
                    alert('Моля, въведете коректна дължина на билото');
                    return;
                }

                if (!document.getElementById('iko-new-starter').value) {
                    alert('Моля, изберете материал за стартовата лента');
                    return;
                }

                if (!document.getElementById('iko-new-ridge-material').value) {
                    alert('Моля, изберете керемиди за билото');
                    return;
                }

                if (!document.getElementById('iko-new-underlay').value) {
                    alert('Моля, изберете подложен килим');
                    return;
                }

                // Get selected materials with proper number parsing
                const collection = document.getElementById('iko-new-collection');
                const selectedCollection = collection.options[collection.selectedIndex];
                const mainCoef = parseEuropeanNumber(selectedCollection.dataset.main);

                const starter = document.getElementById('iko-new-starter');
                const selectedStarter = starter.options[starter.selectedIndex];
                const starterCoef = parseEuropeanNumber(selectedStarter.dataset.coef);

                const ridgeMaterial = document.getElementById('iko-new-ridge-material');
                const selectedRidgeMaterial = ridgeMaterial.options[ridgeMaterial.selectedIndex];
                const ridgeMaterialCoef = parseEuropeanNumber(selectedRidgeMaterial.dataset.coef);

                const underlay = document.getElementById('iko-new-underlay');
                const selectedUnderlay = underlay.options[underlay.selectedIndex];

                // Проверка за нулеви коефициенти
                if (mainCoef === 0) {
                    alert('Грешка: Коефициентът на основните керемиди не може да бъде нулев');
                    return;
                }

                if (starterCoef === 0) {
                    alert('Грешка: Коефициентът на стартовия материал не може да бъде нулев');
                    return;
                }

                if (ridgeMaterialCoef === 0) {
                    alert('Грешка: Коефициентът на материала за билото не може да бъде нулев');
                    return;
                }

                // Calculations
                const roofArea = length * slope * 2; // Assuming 2 slopes
                const mainShingles = Math.ceil(roofArea / mainCoef);
                const starterShingles = Math.ceil((length * 2) / starterCoef);
                const ridgeShingles = Math.ceil(ridge / ridgeMaterialCoef);

                let underlayRolls;
                let underlayType = '';

                if (angle === "21-85") {
                    // За стръмни покриви (21-85°) - коефициент от полето coef_high
                    const underlayCoefHigh = parseEuropeanNumber(selectedUnderlay.dataset.high);
                    if (underlayCoefHigh === 0) {
                        alert('Грешка: Коефициентът на подложния килим за стръмни покриви не може да бъде нулев');
                        return;
                    }
                    underlayRolls = Math.ceil(roofArea / underlayCoefHigh);
                    underlayType = 'стръмен покрив (21-85°)';
                } else if (angle === "9.5-20") {
                    // За полегати покриви (9.5-20°) - коефициент от полето coef_low
                    const underlayCoefLow = parseEuropeanNumber(selectedUnderlay.dataset.low);
                    if (underlayCoefLow === 0) {
                        alert('Грешка: Коефициентът на подложния килим за полегати покриви не може да бъде нулев');
                        return;
                    }
                    underlayRolls = Math.ceil(roofArea / underlayCoefLow);
                    underlayType = 'полегат покрив (9.5-20°)';
                } else {
                    underlayRolls = 0;
                    underlayType = 'неизвестен наклон';
                }

                const nailsKg = (roofArea / 15).toFixed(2);
                const glueTubes = Math.ceil(roofArea / 15);
                const ventilationMeters = (ridge / 0.9).toFixed(1);

                // Display results
                const resultsBody = document.getElementById('iko-new-results-body');
                resultsBody.innerHTML = `
            <tr>
                <td><span>Основни керемиди:</span> ${selectedCollection.value}</td>
                <td>${mainShingles} уп.</td>
            </tr>
            <tr>
                <td><span>Стартова лента: </span>${selectedStarter.value}</td>
                <td>${starterShingles} уп.</td>
            </tr>
            <tr>
                <td><span>Било:</span> ${selectedRidgeMaterial.value}</td>
                <td>${ridgeShingles} уп.</td>
            </tr>
            <tr>
                <td><span>Подложен килим:</span> ${selectedUnderlay.value} (${underlayType})</td>
                <td>${underlayRolls} рул.</td>
            </tr>
            <tr>
                <td>Вент. елемент на билото</td>
                <td>${ventilationMeters} л.м.</td>
            </tr>
            <tr>
                <td>Лепило</td>
                <td>${glueTubes} туб.</td>
            </tr>
            <tr>
                <td>Пирони</td>
                <td>${nailsKg} кг</td>
            </tr>
            <tr style="background-color: white;">
                <td><strong>Параметри на изчислението:</strong></td>
                <td><strong>Стойности</strong></td>
            </tr>
            <tr>
                <td>Площ на покрива</td>
                <td>${roofArea} м²</td>
            </tr>
            <tr>
                <td>Наклон</td>
                <td>${angle === "21-85" ? "21-85°" : "9.5-20°"}</td>
            </tr>
            <tr>
                <td>Коефициент на подложката</td>
                <td>${angle === "21-85" ? parseEuropeanNumber(selectedUnderlay.dataset.high) : parseEuropeanNumber(selectedUnderlay.dataset.low)} м²/рул</td>
            </tr>
        `;
            }
        });
    </script>
    <style>
        /* IKO Roof Calculator styles */
        .iko-new-calculator-container label{
            display: block;
            margin-bottom: 10px!important;
            font-weight: bold!important;
            font-size: 16px !important;
        }
        .iko-calc-img{
            max-width: 500px!important;
            margin-left: -18px;

        }
        #iko-new-results-body td span{
            position: absolute;
            margin-left: -13px;
            margin-top: -17px;
            font-size: 13px;
            color: #dc2626;
            font-weight: 400;
        }
        #enter-params{
            margin-left: 0!important;
            margin-top: -20px!important;
            position: relative!important;
            font-size: 1em!important;
            color: #000000!important;
            font-weight: 600!important;
        }
        .res-calc {
            display: flex;
            flex-direction: column;
            gap: 20px;
            justify-content: center;
            align-items: center;
            position: absolute;
            bottom: 34%;
            width: 91%;
            background-color: transparent !important;
            border: 0 !important;
            font-size: 20px;
        }
        .res-calc svg{
            width: 50px!important;
            height: 50px!important;
            fill: #ef4444;
        }
        .grid-3{
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-top: 25px;
            max-width: 263px;
            position: absolute;
            right: 26px;
            top: 305px;
        }
        .calc-section{
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 50px;
            color: black!important;
            max-width: 1140px!important;
            width: 100vw;
        }
        .iko-new-calculator-container {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }

        .iko-new-calculator-container h1 {
            font-size: 1.5em!important;
            margin-bottom: 20px;
            color: #333!important;
        }

        .iko-new-calculator-container h2 {
            font-size: 1.2em!important;
            margin: 20px 0 10px;
            color: #333!important;
        }

        .iko-new-form-group {
            margin-bottom: 25px;
        }

        .iko-new-form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 16px!important;
        }

        .iko-new-form-group input,
        .iko-new-form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px!important;
            color: black!important;
        }

        .iko-new-results {
            padding: 15px;
            background: #f9f9f9;
            border-radius: 4px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .iko-new-results-table {
            width: 100%;
            border-collapse: collapse;
        }

        .iko-new-results-table td {
            padding: 21px 8px 10px 23px;
            border-bottom: 1px solid #eee;
            font-size: 16px!important;
        }

        .iko-new-results-table td:first-child {
            font-weight: bold;
        }

        .iko-new-form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .iko-new-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background: #dc2626;
            color: white;
            cursor: pointer;
            font-size: 16px;
            transition: .4;
        }

        .iko-new-btn:hover {
            transition: .4;
            transform: scale(1.05);
        }

        #iko-new-reset {
            background: #ccc;
            color: #333;
        }

        #iko-new-reset:hover {
            background: #bbb;
        }
        @media screen and (max-width: 991px){
            .calc-section{
                grid-template-columns: 1fr;
                width: 93vw!important;
            }
            .grid-3 {
                display: grid;
                grid-template-columns: 1fr;
                gap: 10px;
                margin-top: 25px;
                max-width: 100%;
                position: relative;
                right: 0;
                font-size: 14px;
                top: 0;
            }

            .iko-calc-img {
                max-width: 100%!important;
                margin-left: auto;
            }
            .res-calc{
                position: relative;
            }
        }
        @media (max-width: 1200px) and (min-width: 991px){
            .grid-3{
                display: grid;
                grid-template-columns: 1fr 1fr 1fr;
                gap: 10px;
                margin-top: 25px;
                max-width: 100%;
                position: relative;
                right: 0;
                font-size: 14px;
                top: 0;
            }
            .iko-calc-img{
                margin: auto;
            }
        }
    </style>
    <?php
    return ob_get_clean();
}
