<?php

/**
 * Potts Administration Shortcuts for webtrees 2.2.
 */

declare(strict_types=1);

namespace PottsAdminShortcuts;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\RequestHandlers\UserListPage;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleBlockTrait;
use Fisharebest\Webtrees\Module\ModuleConfigInterface;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\View;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

use function array_intersect;
use function array_keys;
use function array_values;
use function is_array;
use function is_string;
use function json_decode;
use function json_encode;
use function route;

final class PottsAdminShortcutsModule extends AbstractModule implements ModuleBlockInterface, ModuleCustomInterface
{
    use ModuleBlockTrait;
    use ModuleCustomTrait;

    private const USER_ADMINISTRATION_SHORTCUT = '@user-administration';

    public function title(): string
    {
        return I18N::translate('Administration shortcuts');
    }

    public function description(): string
    {
        return I18N::translate("Quick links to an administrator's favourite module settings and user administration.");
    }

    public function isEnabledByDefault(): bool
    {
        return false;
    }

    public function customModuleVersion(): string
    {
        return '1.0.0-beta.1';
    }

    public function customModuleAuthorName(): string
    {
        return 'Jason Potts';
    }

    public function customModuleSupportUrl(): string
    {
        return 'https://github.com/PottsNet/potts-admin-shortcuts/issues';
    }

    public function resourcesFolder(): string
    {
        return __DIR__ . '/resources/';
    }

    public function boot(): void
    {
        View::registerNamespace('potts-admin-shortcuts', $this->resourcesFolder() . 'views/');

        View::pushunique('styles');
        echo '<style id="potts-admin-shortcuts-styles">' . $this->styles() . '</style>';
        View::endpushunique();
    }

    /**
     * @param array<string,string> $config
     */
    public function getBlock(Tree $tree, int $block_id, string $context, array $config = []): string
    {
        if (!Auth::isAdmin(Auth::user())) {
            return '';
        }

        $available = $this->availableShortcuts();
        $shortcuts = [];

        foreach ($this->selectedShortcutIds($block_id) as $shortcut_id) {
            if (isset($available[$shortcut_id])) {
                $shortcuts[] = $available[$shortcut_id];
            }
        }

        $content = view('potts-admin-shortcuts::block', [
            'shortcuts' => $shortcuts,
        ]);

        if ($context === self::CONTEXT_EMBED) {
            return $content;
        }

        return view('modules/block-template', [
            'block'      => Str::kebab($this->name()),
            'id'         => $block_id,
            'config_url' => $this->configUrl($tree, $context, $block_id),
            'title'      => $this->title(),
            'content'    => $content,
        ]);
    }

    public function loadAjax(): bool
    {
        return false;
    }

    public function isUserBlock(): bool
    {
        return true;
    }

    public function isTreeBlock(): bool
    {
        return false;
    }

    public function editBlockConfiguration(Tree $tree, int $block_id): string
    {
        if (!Auth::isAdmin(Auth::user())) {
            return '<p>' . I18N::translate('This block is available to administrators only.') . '</p>';
        }

        return view('potts-admin-shortcuts::config', [
            'available' => $this->availableShortcuts(),
            'selected'  => $this->selectedShortcutIds($block_id),
        ]);
    }

    public function saveBlockConfiguration(ServerRequestInterface $request, int $block_id): void
    {
        if (!Auth::isAdmin(Auth::user())) {
            return;
        }

        $parsed    = $request->getParsedBody();
        $data      = is_array($parsed) ? $parsed : [];
        $submitted = isset($data['shortcuts']) && is_array($data['shortcuts'])
            ? $data['shortcuts']
            : [];
        $selected  = [];

        foreach ($submitted as $shortcut_id) {
            if (is_string($shortcut_id)) {
                $selected[] = $shortcut_id;
            }
        }

        $valid    = array_keys($this->availableShortcuts());
        $selected = array_values(array_intersect($valid, $selected));
        $encoded  = json_encode($selected);

        $this->setBlockSetting($block_id, 'shortcuts', $encoded === false ? '[]' : $encoded);
    }

    /**
     * @return array<string,array{title:string,url:string,type:string}>
     */
    private function availableShortcuts(): array
    {
        $shortcuts = [
            self::USER_ADMINISTRATION_SHORTCUT => [
                'title' => I18N::translate('User administration'),
                'url'   => route(UserListPage::class),
                'type'  => 'users',
            ],
        ];

        $modules = Registry::container()
            ->get(ModuleService::class)
            ->findByInterface(ModuleConfigInterface::class, false, true);

        foreach ($modules as $module) {
            $shortcuts[$module->name()] = [
                'title' => $module->title(),
                'url'   => $module->getConfigLink(),
                'type'  => $module instanceof ModuleCustomInterface ? 'custom' : 'module',
            ];
        }

        return $shortcuts;
    }

    /**
     * @return array<int,string>
     */
    private function selectedShortcutIds(int $block_id): array
    {
        $stored = $this->getBlockSetting($block_id, 'shortcuts', '');

        if ($stored === '') {
            return [self::USER_ADMINISTRATION_SHORTCUT];
        }

        $decoded = json_decode($stored, true);

        if (!is_array($decoded)) {
            return [];
        }

        $selected = [];

        foreach ($decoded as $shortcut_id) {
            if (is_string($shortcut_id)) {
                $selected[] = $shortcut_id;
            }
        }

        return $selected;
    }

    private function styles(): string
    {
        return '
.potts-admin-shortcuts{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:.65rem}
.potts-admin-shortcut{display:grid;grid-template-columns:2rem minmax(0,1fr) auto;align-items:center;gap:.7rem;min-height:3.35rem;padding:.65rem .8rem;border:1px solid rgba(35,48,56,.16);border-radius:8px;background:#fff;color:#123b4b;font-weight:700;text-decoration:none}
.potts-admin-shortcut:hover,.potts-admin-shortcut:focus-visible{border-color:rgba(24,90,113,.42);background:#f7faf9;color:#185a71}
.potts-admin-shortcut-icon{display:grid;width:1.8rem;height:1.8rem;place-items:center;border-radius:6px;background:#185a71;color:#fff}
.potts-admin-shortcut-icon svg{width:1rem;height:1rem;fill:currentColor}
.potts-admin-shortcut-arrow{color:#9a6b25;font-size:1.4rem;line-height:1}
.potts-admin-shortcuts-config{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:.5rem 1rem;max-height:min(55vh,32rem);padding:.25rem;overflow:auto}
.potts-admin-shortcuts-config .form-check{margin:0;padding:.55rem .65rem .55rem 2.15rem;border:1px solid rgba(35,48,56,.16);border-radius:6px;background:#fff}
';
    }
}
