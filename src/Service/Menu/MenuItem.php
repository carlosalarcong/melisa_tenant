<?php

namespace App\Service\Menu;

/**
 * Representa un item del menú con sus propiedades y permisos.
 */
class MenuItem
{
    public function __construct(
        private string $name,
        private string $label,
        private ?string $route = null,
        private ?string $icon = null,
        private array $requiredRoles = [],
        private ?string $module = null,
        private array $children = [],
        private ?string $badge = null,
        private ?string $badgeColor = null
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getRequiredRoles(): array
    {
        return $this->requiredRoles;
    }

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return !empty($this->children);
    }

    public function getBadge(): ?string
    {
        return $this->badge;
    }

    public function getBadgeColor(): ?string
    {
        return $this->badgeColor;
    }

    public function addChild(MenuItem $child): void
    {
        $this->children[] = $child;
    }

    /**
     * Convierte el MenuItem a array para usar en templates.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'route' => $this->route,
            'icon' => $this->icon,
            'module' => $this->module,
            'badge' => $this->badge,
            'badge_color' => $this->badgeColor,
            'children' => array_map(fn(MenuItem $child) => $child->toArray(), $this->children),
        ];
    }
}
