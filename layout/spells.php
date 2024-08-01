<?php
require_once 'engine/init.php';
include 'layout/overall/header.php';


// Loading spell list
$spellsCache = new Cache('engine/cache/spells');
if (user_logged_in() && is_admin($user_data)) {
    if (isset($_GET['update'])) {
        echo "<p><strong>Logged in as admin, loading engine/XML/spells.xml file and updating cache.</strong></p>";

        //lua parts 
        // Función para obtener los tipos de combate de un archivo script
$cacheDir = 'C:\xampp\htdocs\engine\cache\cache\cache'; // Directorio donde se almacenará el caché
//$cacheTimestamp = 0; // Inicializar el timestamp del caché
      //  $cacheTimestamp = time();

function getCombatTypes($fullPath, &$totalFiles, &$processedFiles, &$unprocessedFiles) {
    $fileName = basename($fullPath);
    $fileNameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
    $cleanFileName = str_replace([' ', '_'], '', $fileNameWithoutExtension);

    // Initialize $combatTypes if not already set
    global $combatTypes;
    if (!isset($combatTypes)) {
        $combatTypes = [];
    }

    // Initialize $combatTypeData with default values
    $combatTypeData = [
        'combatParams' => [],
        'effectParams' => [],
        'distanceEffectParams' => [],
        'magicEffectParams' => [],
    ];

    // Check if the file has already been processed
    if (isset($combatTypes[$fileName])) {
        // The file has already been processed, return the existing data
        return $combatTypes[$fileName];
    }

    // Check if the file has already been processed by cleanFileName
    if (isset($combatTypes[$cleanFileName])) {
        return $combatTypes[$cleanFileName];
    }

    // Initialize arrays to store parameters
    $combatParams = [];
    $effectParams = [];
    $distanceEffectParams = [];
    $magicEffectParams = []; // Initialize magicEffectParams as an empty array

    // Check if the file exists
    if (file_exists($fullPath)) {
        $totalFiles++;
        $content = file_get_contents($fullPath);

        // Regex patterns to capture combat and effect parameters
        $pattern = '/combat:setParameter\((COMBAT_PARAM_TYPE|COMBAT_PARAM_EFFECT|COMBAT_PARAM_DISTANCEEFFECT), (COMBAT_\w+|CONST_\w+)\)/';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        // Regex pattern to capture sendMagicEffect parameters
        $magicEffectPattern = '/sendMagicEffect\((CONST_\w+)\)/';
        preg_match_all($magicEffectPattern, $content, $magicMatches, PREG_SET_ORDER);

        // Merge magicMatches into magicEffectParams if not already included
        foreach ($magicMatches as $match) {
            $paramValue = $match[1];
            if (!in_array($paramValue, $magicEffectParams)) {
                $magicEffectParams[] = $paramValue;
            }
        }

        // Process matched parameters
        foreach ($matches as $match) {
            $paramType = $match[1];
            $paramValue = $match[2];

            if ($paramType === 'COMBAT_PARAM_TYPE') {
                $combatParams[] = $paramValue;
            } elseif ($paramType === 'COMBAT_PARAM_EFFECT') {
                $effectParams[] = $paramValue;
            } elseif ($paramType === 'COMBAT_PARAM_DISTANCEEFFECT') {
                $distanceEffectParams[] = $paramValue;
            }
        }

        // Store the script data in the $combatTypes array
        $combatTypes[$fileName] = [
            'combatParams' => array_unique($combatParams),
            'effectParams' => array_unique($effectParams),
            'distanceEffectParams' => array_unique($distanceEffectParams),
            'magicEffectParams' => array_unique($magicEffectParams),
        ];

        echo "Processing script: " . basename($fullPath) . "<br>";
        $processedFiles++;
        return $combatTypes[$fileName];
    }

    echo "File does not exist: $fullPath<br>";
    $unprocessedFiles++;
    return [
        'combatParams' => ['Unknown'],
        'effectParams' => ['Unknown'],
        'distanceEffectParams' => ['Unknown'],
        'magicEffectParams' => ['Unknown'],
    ];
}

function getAllScripts($directory) {
    $files = [];
    $iterator = new RecursiveDirectoryIterator($directory);
    $iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
    $recursive = new RecursiveIteratorIterator($iterator);

    foreach ($recursive as $file) {
        if ($file->isFile()) {
            $files[] = $file->getPathname();
        }
    }

    return $files;
}

// Cargar todos los tipos de combate
// Cargar todos los tipos de combate
$combatTypes = [];
$scriptDir = 'C:/Users/felip/Documents/GitHub/pro-ot/data/spells/scripts/';
$scripts = getAllScripts($scriptDir);

$totalFiles = 0;
$processedFiles = 0;
$unprocessedFiles = 0;

foreach ($scripts as $script) {
    $scriptName = basename($script);
    echo "Cargando script: " . $scriptName . "<br>";
    $combatTypes[$scriptName] = getCombatTypes($script, $totalFiles, $processedFiles, $unprocessedFiles);

    // Use the $combatTypeData variable here
    $combatTypeData = isset($combatTypes[$scriptName]) ? $combatTypes[$scriptName] : [
        'combatParams' => ['Unknown'],
        'effectParams' => ['Unknown'],
        //'distanceEffectParams' => ['Unknown'],
        'magicEffectParams' => ['Unknown'],
    ];

    // Assign combatTypeData to $spells array
    // Assuming you have defined $type and $name somewhere else in your code
    if (isset($type) && isset($name)) {
        $spells[$type][$name]['combatType'] = [
            'combatParams' => $combatTypeData['combatParams'],
            'effectParams' => $combatTypeData['effectParams'],
            'magicEffectParams' => $combatTypeData['magicEffectParams'],
        ];
    } else {
        echo "Warning: \$type and/or \$name are not defined.<br>";
    }
}
// Cargar todos los tipos de combate
$combatTypes = [];
$scriptDir = 'C:/Users/felip/Documents/GitHub/pro-ot/data/spells/scripts/';
$scripts = getAllScripts($scriptDir);

$totalFiles = 0;
$processedFiles = 0;
$unprocessedFiles = 0;

foreach ($scripts as $script) {
    $scriptName = basename($script);
    echo "Cargando script: " . $scriptName . "<br>";
    $combatTypes[$scriptName] = getCombatTypes($script, $totalFiles, $processedFiles, $unprocessedFiles);

    // Use the $combatTypeData variable here
    $combatTypeData = isset($combatTypes[$scriptName]) ? $combatTypes[$scriptName] : [
        'combatParams' => ['Unknown'],
        'effectParams' => ['Unknown'],
        'distanceEffectParams' => ['Unknown'],
        'magicEffectParams' => ['Unknown'],
    ];

    // Assign combatTypeData to $spells array
    // Assuming you have defined $type and $name somewhere else in your code
    if (isset($type) && isset($name)) {
        $spells[$type][$name]['combatType'] = [
            'combatParams' => $combatTypeData['combatParams'],
            'effectParams' => $combatTypeData['effectParams'],
            'magicEffectParams' => $combatTypeData['magicEffectParams'],
        ];
    } else {
        echo "Warning: \$type and/or \$name are not defined.<br>";
    }
}
// Guardar los tipos de combate en el caché (asegúrate de tener la lógica de caché implementada)
// $spellsCache->save('combat_types', $combatTypes);

echo "<p>Tipos de combate actualizados en el caché.</p>";
echo "<p>Total scripts: $totalFiles</p>";
echo "<p>Processed scripts: $processedFiles</p>";
echo "<p>Unprocessed scripts: $unprocessedFiles</p>";
// Imprime el contenido de $combatTypes para verificar
//echo "<pre>";
//echo json_encode($combatTypes, JSON_PRETTY_PRINT);
//echo "</pre>";
        // SPELLS XML TO PHP ARRAY
        $spellsXML = simplexml_load_file("engine/XML/spells.xml");
        if ($spellsXML !== false) {
            $types = array();
            $type_attr = array();
            $groups = array();

            // This empty array will eventually contain all spells grouped by type and indexed by spell name
            $spells = array();

            // Loop through each XML spell object
            foreach ($spellsXML as $type => $spell) {
                // Get spell types
                if (!in_array($type, $types)) {
                    $types[] = $type;
                    $type_attr[$type] = array();
                }
                // Get spell attributes
                $attributes = array();
                // Extract attribute values from the XML object and store it in a more manage friendly way $attributes
                foreach ($spell->attributes() as $aName => $aValue)
                    $attributes["$aName"] = "$aValue";

                // Alias attributes
                if (isset($attributes['level'])) $attributes['lvl'] = $attributes['level'];
                if (isset($attributes['magiclevel'])) $attributes['maglv'] = $attributes['magiclevel'];

                // Populate type attributes
                foreach (array_keys($attributes) as $attr) {
                    if (!in_array($attr, $type_attr[$type]))
                        $type_attr[$type][] = $attr;
                }
                // Get spell groups
                if (isset($attributes['group'])) {
                    if (!in_array($attributes['group'], $groups))
                        $groups[] = $attributes['group'];
                }
                // Get spell vocations
                $vocations = array();
                foreach ($spell->vocation as $vocation) {
                    foreach ($vocation->attributes() as $attributeName => $attributeValue) {
                        if ("$attributeName" == "name") {
                            $vocId = vocation_name_to_id("$attributeValue");
                            $vocations[] = ($vocId !== false) ? $vocId : "$attributeValue";
                        } elseif ("$attributeName" == "id") {
                            $vocations[] = (int)"$attributeValue";
                        }
                    }
                }
                // Exclude monster spells and house spells
                $words = (isset($attributes['words'])) ? $attributes['words'] : false;
                $name = (isset($attributes['name'])) ? $attributes['name'] : false;
                if (substr($words, 0, 3) !== '###' && substr($name, 0, 5) !== 'House') {
                    $spells[$type][$name] = array('vocations' => $vocations);
                    foreach ($type_attr[$type] as $att)
                        $spells[$type][$name][$att] = (isset($attributes[$att])) ? $attributes[$att] : false;
                    
              // Assign combat type
//if (isset($attributes['script'])) {
//    $scriptName = basename($attributes['script']);

//    $combatTypeData = isset($combatTypes[$scriptName]) ? $combatTypes[$scriptName] : [
//        'combatParams' => ['Unknown'],
//        'effectParams' => ['Unknown'],
//        'distanceEffectParams' => ['Unknown'],
//    ];
//    $spells[$type][$name]['combatType'] = [
//        'combatParams' => $combatTypeData['combatParams'],
//        'effectParams' => $combatTypeData['effectParams']
//    ];
//}
                    if (isset($attributes['script'])) {
    // Assuming $attributes['script'] contains the path to levitate.lua
$scriptName = basename($attributes['script']);
 $scriptNameWithoutExtension = pathinfo($scriptName, PATHINFO_FILENAME);
    $cleanScriptName = str_replace([' ', '_'], '', $scriptNameWithoutExtension);
global $combatTypes; // Ensure $combatTypes is global

// Special handling for levitate.lua
if ($scriptName === 'levitate.lua') {
    // Define the combat and effect parameters for levitate.lua
    $combatTypeData = [
        'combatParams' => ['CONST_ME_TELEPORT'], // You can adjust this if levitate.lua provides combat type information
        'effectParams' => ['CONST_ME_TELEPORT'],
        'distanceEffectParams' => ['CONST_ME_TELEPORT'], // Assuming no distance effects are defined
    ];
}// else {
 //Default handling for other scripts
$combatTypeData = isset($combatTypes[$scriptName]) ? $combatTypes[$scriptName] : [
    'combatParams' => isset($combatTypeData['combatParams']) ? $combatTypeData['combatParams'] : ['Unknown'],
    'effectParams' => isset($combatTypeData['effectParams']) ? $combatTypeData['effectParams'] : ['Unknown'],
    //'distanceEffectParams' => isset($combatTypeData['distanceEffectParams']) ? $combatTypeData['distanceEffectParams'] : ['Unknown'],
    'magicEffectParams' => isset($combatTypeData['magicEffectParams']) ? $combatTypeData['magicEffectParams'] : ['Unknown'],
];
//$combatTypeData = isset($combatTypes[$scriptName]) ? $combatTypes[$scriptName] : [
//    'combatParams' => ['Unknown'],
//    'effectParams' => ['Unknown'],
//    'distanceEffectParams' => ['Unknown'],
//    'magicEffectParams' => ['Unknown'],
//];
$spells[$type][$name]['combatType'] = [
    'combatParams' => $combatTypeData['combatParams'],
    'effectParams' => $combatTypeData['effectParams'],
    'magicEffectParams' => $combatTypeData['magicEffectParams'],
];
//} else {
  //  $spells[$type][$name]['combatType'] = [
    //    'combatParams' => ['Unknown'],
      //  'effectParams' => ['Unknown']
    //];
//}
                }
            }

            // Sort the spell list properly
            foreach (array_keys($spells) as $type) {
                usort($spells[$type], function ($a, $b) {
                    if (isset($a['lvl']))
                        return $a['lvl'] - $b['lvl'];
                    if (isset($a['maglv']))
                        return $a['maglv'] - $b['maglv'];
                    return -1;
                });
            }
            $spellsCache->setContent($spells);
            $spellsCache->save();
        } //else {
            echo "<p><strong>Failed to load engine/XML/spells.xml file.</strong></p>";
        }
    } else {
        $spells = $spellsCache->load();
        ?>
        <form action="">
            <input type="submit" name="update" value="Generate new cache">
        </form>
        <?php
    }
} else {
    $spells = $spellsCache->load();
}

if ($spells) {
    // Preparing data
    $configVoc = $config['vocations'];
    $types = array_keys($spells);
    $itemServer = 'https://forgottenot.online/'.$config['shop']['imageServer'].'/';

    // Filter spells by vocation
    $getVoc = (isset($_GET['vocation'])) ? getValue($_GET['vocation']) : 'all';
    if ($getVoc !== 'all') {
        $getVoc = (int)$getVoc;
        foreach ($types as $type) {
            foreach ($spells[$type] as $name => $spell) {
                if (!empty($spell['vocations'])) {
                    if (!in_array($getVoc, $spell['vocations'])) {
                        unset($spells[$type][$name]);
                    }
                }
            }
        }
    }
    // Render HTML
    ?>

    <h1 id="spells">Spells<?php if ($getVoc !== 'all') echo ' ('.$configVoc[$getVoc]['name'].')';?></h1>

    <form action="#spells" class="filter_spells">
        <label for="vocation">Filter vocation:</label>
        <select id="vocation" name="vocation">
            <option value="all">All</option>
            <?php foreach ($config['vocations'] as $id => $vocation): ?>
                <option value="<?php echo $id; ?>" <?php if ($getVoc === $id) echo "selected"; ?>><?php echo $vocation['name']; ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Search">
    </form>

    <h2>Spell types:</h2>
    <ul>
        <?php foreach ($types as $type): ?>
        <li><a href="#spell_<?php echo $type; ?>"><?php echo ucfirst($type); ?></a></li>
        <?php endforeach; ?>
    </ul>

    <h2 id="spell_instant">Instant Spells</h2>
<a href="#spells">Jump to top</a>
<table class="table tbl-hover">
    <tbody>
        <tr class="yellow">
            <td>Name</td>
            <td>Combat Type</td>
            <td>Words</td>
            <td>Level</td>
            <td>Mana</td>
            <td>Vocations</td>
        </tr>

        <?php foreach ($spells['instant'] as $spell): ?>
        <tr>
            <td><?php echo htmlspecialchars($spell['name']); ?></td>
            <td>
    <?php
    if (isset($spell['combatType']) && is_array($spell['combatType'])) {
        $combatImages = [];
        $effectImages = [];

        // Check if combatParams and effectParams exist and are arrays
        if (isset($spell['combatType']['combatParams']) && is_array($spell['combatType']['combatParams'])) {
            foreach ($spell['combatType']['combatParams'] as $combatParam) {
                $imageName = strtolower(str_replace('COMBAT_', '', $combatParam));
                $combatImages[] = "<span><img src='layout/spellsimages/{$imageName}.gif' alt='{$combatParam}'> " . htmlspecialchars($combatParam) . "</span>";
            }
        }

        // Check if effectParams exist and is an array
        if (isset($spell['combatType']['effectParams']) && is_array($spell['combatType']['effectParams'])) {
            foreach ($spell['combatType']['effectParams'] as $effectParam) {
                $imageName = strtolower(str_replace('CONST_ME_', '', $effectParam));
                $effectImages[] = "<span><img src='layout/spellsimages/{$imageName}.gif' alt='{$effectParam}'> " . htmlspecialchars($effectParam) . "</span>";
            }
        }

        // Check if effectParams exist and is an array
        if (isset($spell['combatType']['magicEffectParams']) && is_array($spell['combatType']['magicEffectParams'])) {
            foreach ($spell['combatType']['magicEffectParams'] as $magicEffectParam) {
                $imageName = strtolower(str_replace('CONST_ME_', '', $magicEffectParam));
                $effectImages[] = "<span><img src='layout/spellsimages/{$imageName}.gif' alt='{$magicEffectParam}'> " . htmlspecialchars($magicEffectParam) . "</span>";
            }
        }

        // Output combatImages and effectImages
        echo implode('<br>', $combatImages);
        echo '<br>'; // Ensure a line break between combat and effect images
        echo implode('<br>', $effectImages);
    } else {
        // If combatType is Unknown, display placeholder images 225 to 228 alternately
        $placeholders = [];
        for ($i = 225; $i <= 228; $i++) {
            $placeholders[] = "<span><img src='layout/spellsimages/{$i}.gif' alt='Unknown'> Unknown</span>";
        }
        echo implode('<br>', $placeholders);
    }
    ?>
</td>

            <td><?php echo htmlspecialchars($spell['words']); ?></td>
            <td><?php echo htmlspecialchars($spell['lvl']); ?></td>
            <td><?php echo htmlspecialchars($spell['mana']); ?></td>
            <td>
                <?php
                if (!empty($spell['vocations'])) {
                    if ($getVoc !== 'all') {
                        echo htmlspecialchars($configVoc[$getVoc]['name']);
                    } else {
                        $names = [];
                        foreach ($spell['vocations'] as $id) {
                            if (isset($configVoc[$id])) {
                                $names[] = htmlspecialchars($configVoc[$id]['name']);
                            }
                        }
                        echo implode(',<br>', $names);
                    }
                }
                ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2 id="spell_rune">Magical Runes</h2>

    <a href="#spells">Jump to top</a>
    <table class="table tbl-hover">
        <tbody>
            <tr class="yellow">
                <td>Name</td>
                <td>Combat Type</td>
                <td>Level</td>
                <td>Magic Level</td>
                <td>Image</td>
                <td>Vocations</td>
            </tr>
            <?php foreach ($spells['rune'] as $spell): ?>
            <tr>
                <td><?php echo $spell['name']; ?></td>
               <td>
   <?php
if (isset($spell['combatType']) && is_array($spell['combatType'])) {
    $combatParams = $spell['combatType']['combatParams'];
    $effectParams = $spell['combatType']['effectParams'];
?>
    <tr>
        <td><?php echo $spell['name']; ?></td>
<td>
<?php
if (isset($spell['combatType']) && is_array($spell['combatType'])) {
    $combatImages = [];
    $effectImages = [];

    // Check if combatParams and effectParams exist and are arrays
    if (isset($spell['combatType']['combatParams']) && is_array($spell['combatType']['combatParams'])) {
        foreach ($spell['combatType']['combatParams'] as $combatParam) {
            $imageName = strtolower(str_replace('COMBAT_', '', $combatParam));
            $combatImages[] = "<span><img src='layout/spellsimages/{$imageName}.gif' alt='{$combatParam}'> " . htmlspecialchars($combatParam) . "</span>";
        }
    }

    // Check if effectParams exist and is an array
    if (isset($spell['combatType']['effectParams']) && is_array($spell['combatType']['effectParams'])) {
        foreach ($spell['combatType']['effectParams'] as $effectParam) {
            $imageName = strtolower(str_replace('CONST_ME_', '', $effectParam));
            $effectImages[] = "<span><img src='layout/spellsimages/{$imageName}.gif' alt='{$effectParam}'> " . htmlspecialchars($effectParam) . "</span>";
        }
    }

    // Output combatImages and effectImages
    echo implode('<br>', $combatImages);
    echo implode('<br>', $effectImages);
} else {
    // If combatType is Unknown, display the placeholder image 225.gif
    echo "<span><img src='layout/spellsimages/225.gif' alt='Unknown'> Unknown</span>";
}
?>
</td>
        <!-- Other table columns -->
    </tr>
<?php
} else {
    echo 'Unknown';
}
?>
</td>
                <td><?php echo $spell['lvl']; ?></td>
                <td><?php echo $spell['maglv']; ?></td>
                <td><img src="<?php echo $itemServer.$spell['id'].'.gif'; ?>" alt="Rune image"></td>
                <td><?php
                if (!empty($spell['vocations'])) {
                    if ($getVoc !== 'all') {
                        echo $configVoc[$getVoc]['name'];
                    } else {
                        $names = array();
                        foreach ($spell['vocations'] as $id) {
                            if (isset($configVoc[$id]))
                                $names[] = $configVoc[$id]['name'];
                        }
                        echo implode(',<br>', $names);
                    }
                }
                ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (isset($spells['conjure'])): ?>
    <h2 id="spell_conjure">Conjure Spells</h2>
    <a href="#spells">Jump to top</a>
    <table class="table tbl-hover">
        <tbody>
            <tr class="yellow">
                <td>Name</td>
                <td>Combat Type</td>
                <td>Words</td>
                <td>Level</td>
                <td>Mana</td>
                <td>Soul</td>
                <td>Charges</td>
                <td>Image</td>
                <td>Vocations</td>
            </tr>
            <?php foreach ($spells['conjure'] as $spell): ?>
            <tr>
                <td><?php echo $spell['name']; ?></td>
                <td>
    <?php
    if (isset($spell['combatType']) && is_array($spell['combatType'])) {
        echo "Combat: " . (empty($spell['combatType']['combatParams']) ? 'Unknown' : implode(', ', $spell['combatType']['combatParams'])) . "<br>";
        echo "Effect: " . (empty($spell['combatType']['effectParams']) ? 'Unknown' : implode(', ', $spell['combatType']['effectParams']));
    } else {
        echo 'Unknown';
    }
    ?>
</td>
                <td><?php echo $spell['words']; ?></td>
                <td><?php echo $spell['lvl']; ?></td>
                <td><?php echo $spell['mana']; ?></td>
                <td><?php echo $spell['soul']; ?></td>
                <td><?php echo $spell['conjureCount']; ?></td>
                <td><img src="<?php echo $itemServer.$spell['conjureId'].'.gif'; ?>" alt="Rune image"></td>
                <td><?php
                if (!empty($spell['vocations'])) {
                    if ($getVoc !== 'all') {
                        echo $configVoc[$getVoc]['name'];
                    } else {
                        $names = array();
                        foreach ($spell['vocations'] as $id) {
                            if (isset($configVoc[$id]))
                                $names[] = $configVoc[$id]['name'];
                        }
                        echo implode(',<br>', $names);
                    }
                }
                ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="#spells">Jump to top</a>
    <?php endif; ?>
    <?php
} else {
    ?>
    <h1>Spells</h1>
    <p>Spells have currently not been loaded into the website by the server admin.</p>
    <?php
}
// Función para obtener la lista manual de parámetros de efecto y tipo de daño de combate

/* Debug tests
foreach ($spells as $type => $spells) {
    data_dump($spells, false, "Type: $type");
}

// All spell attributes?
'group', 'words', 'lvl', 'level', 'maglv', 'magiclevel', 'charges', 'allowfaruse', 'blocktype', 'mana', 'soul', 'prem', 'aggressive', 'range', 'selftarget', 'needtarget', 'blockwalls', 'needweapon', 'exhaustion', 'groupcooldown', 'needlearn', 'casterTargetOrDirection', 'direction', 'params', 'playernameparam', 'conjureId', 'reagentId', 'conjureCount', 'vocations'
*/
