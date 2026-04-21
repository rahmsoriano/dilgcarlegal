<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AdminLayout extends Component
{
    public string $mode;
    public bool $showOpinionsNav;
    public bool $showProfileMenu;
    public bool $showLoginButton;
    public string $loginRoute;
    public string $newChatRoute;
    public string $archiveRoute;
    public string $chatIndexRoute;
    public string $chatShowRoute;
    public string $sidebarUpdateRoute;
    public string $sidebarTogglePinRoute;
    public string $sidebarToggleSaveRoute;
    public string $sidebarDeleteRoute;

    public function __construct(
        string $mode = 'admin',
        ?bool $showOpinionsNav = null,
        ?bool $showProfileMenu = null,
        ?bool $showLoginButton = null,
        string $loginRoute = 'login',
        ?string $newChatRoute = null,
        ?string $archiveRoute = null,
        ?string $chatIndexRoute = null,
        ?string $chatShowRoute = null,
        ?string $sidebarUpdateRoute = null,
        ?string $sidebarTogglePinRoute = null,
        ?string $sidebarToggleSaveRoute = null,
        ?string $sidebarDeleteRoute = null,
    ) {
        $this->mode = $mode;

        $isPublic = $mode === 'public';

        $this->showOpinionsNav = $showOpinionsNav ?? ! $isPublic;
        $this->showProfileMenu = $showProfileMenu ?? ! $isPublic;
        $this->showLoginButton = $showLoginButton ?? $isPublic;

        $this->loginRoute = $loginRoute;

        $this->newChatRoute = $newChatRoute ?? ($isPublic ? 'legal.ai.new' : 'admin.legal.ai.new');
        $this->archiveRoute = $archiveRoute ?? ($isPublic ? 'legal.ai.saved' : 'admin.legal.ai.saved');
        $this->chatIndexRoute = $chatIndexRoute ?? ($isPublic ? 'legal.ai' : 'admin.legal.ai');
        $this->chatShowRoute = $chatShowRoute ?? ($isPublic ? 'legal.ai.show' : 'admin.legal.ai.show');

        $this->sidebarUpdateRoute = $sidebarUpdateRoute ?? ($isPublic ? 'legal.ai.conversations.update' : 'conversations.update');
        $this->sidebarTogglePinRoute = $sidebarTogglePinRoute ?? ($isPublic ? 'legal.ai.conversations.toggle-pin' : 'conversations.toggle-pin');
        $this->sidebarToggleSaveRoute = $sidebarToggleSaveRoute ?? ($isPublic ? 'legal.ai.conversations.toggle-save' : 'conversations.toggle-save');
        $this->sidebarDeleteRoute = $sidebarDeleteRoute ?? ($isPublic ? 'legal.ai.conversations.destroy' : 'conversations.destroy');
    }

    public function render(): View
    {
        return view('layouts.admin');
    }
}
