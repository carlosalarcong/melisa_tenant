<?php

declare(strict_types=1);

namespace App\Tests\Unit\Twig;

use App\Security\FieldAccess;
use App\Security\SecuredResourceInterface;
use App\Twig\SecurityExtension;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class SecurityExtensionTest extends TestCase
{
    private SecurityExtension $extension;
    private Security&MockObject $security;

    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->extension = new SecurityExtension($this->security);
    }

    public function testGetFunctions(): void
    {
        $functions = $this->extension->getFunctions();
        
        $this->assertCount(4, $functions);
        $this->assertEquals('field_access', $functions[0]->getName());
        $this->assertEquals('can_view_field', $functions[1]->getName());
        $this->assertEquals('can_edit_field', $functions[2]->getName());
        $this->assertEquals('can_delete_field', $functions[3]->getName());
    }

    public function testCreateFieldAccess(): void
    {
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('person');
        $resource->method('getPermissionId')->willReturn(1);
        
        $fieldName = 'email';
        
        $result = $this->extension->createFieldAccess($resource, $fieldName);
        
        $this->assertInstanceOf(FieldAccess::class, $result);
        $this->assertSame($resource, $result->resource);
        $this->assertSame($fieldName, $result->field);
    }

    public function testCanViewFieldReturnsTrue(): void
    {
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('person');
        $resource->method('getPermissionId')->willReturn(1);
        
        $fieldName = 'email';
        
        $this->security
            ->expects($this->once())
            ->method('isGranted')
            ->with(
                'VIEW',
                $this->callback(function ($subject) use ($resource, $fieldName) {
                    return $subject instanceof FieldAccess
                        && $subject->resource === $resource
                        && $subject->field === $fieldName;
                })
            )
            ->willReturn(true);
        
        $result = $this->extension->canViewField($resource, $fieldName);
        
        $this->assertTrue($result);
    }

    public function testCanViewFieldReturnsFalse(): void
    {
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('person');
        $resource->method('getPermissionId')->willReturn(1);
        
        $fieldName = 'salary';
        
        $this->security
            ->expects($this->once())
            ->method('isGranted')
            ->with('VIEW', $this->isInstanceOf(FieldAccess::class))
            ->willReturn(false);
        
        $result = $this->extension->canViewField($resource, $fieldName);
        
        $this->assertFalse($result);
    }

    public function testCanEditFieldReturnsTrue(): void
    {
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('person');
        $resource->method('getPermissionId')->willReturn(1);
        
        $fieldName = 'address';
        
        $this->security
            ->expects($this->once())
            ->method('isGranted')
            ->with(
                'EDIT',
                $this->callback(function ($subject) use ($resource, $fieldName) {
                    return $subject instanceof FieldAccess
                        && $subject->resource === $resource
                        && $subject->field === $fieldName;
                })
            )
            ->willReturn(true);
        
        $result = $this->extension->canEditField($resource, $fieldName);
        
        $this->assertTrue($result);
    }

    public function testCanEditFieldReturnsFalse(): void
    {
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('person');
        $resource->method('getPermissionId')->willReturn(1);
        
        $fieldName = 'sensitive_data';
        
        $this->security
            ->expects($this->once())
            ->method('isGranted')
            ->with('EDIT', $this->isInstanceOf(FieldAccess::class))
            ->willReturn(false);
        
        $result = $this->extension->canEditField($resource, $fieldName);
        
        $this->assertFalse($result);
    }

    public function testCanDeleteFieldReturnsTrue(): void
    {
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('person');
        $resource->method('getPermissionId')->willReturn(1);
        
        $fieldName = 'note';
        
        $this->security
            ->expects($this->once())
            ->method('isGranted')
            ->with(
                'DELETE',
                $this->callback(function ($subject) use ($resource, $fieldName) {
                    return $subject instanceof FieldAccess
                        && $subject->resource === $resource
                        && $subject->field === $fieldName;
                })
            )
            ->willReturn(true);
        
        $result = $this->extension->canDeleteField($resource, $fieldName);
        
        $this->assertTrue($result);
    }

    public function testCanDeleteFieldReturnsFalse(): void
    {
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('person');
        $resource->method('getPermissionId')->willReturn(1);
        
        $fieldName = 'permanent_record';
        
        $this->security
            ->expects($this->once())
            ->method('isGranted')
            ->with('DELETE', $this->isInstanceOf(FieldAccess::class))
            ->willReturn(false);
        
        $result = $this->extension->canDeleteField($resource, $fieldName);
        
        $this->assertFalse($result);
    }

    public function testMultipleFieldChecksOnSameResource(): void
    {
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('patient');
        $resource->method('getPermissionId')->willReturn(42);
        
        $this->security
            ->expects($this->exactly(3))
            ->method('isGranted')
            ->willReturnOnConsecutiveCalls(true, false, true);
        
        $this->assertTrue($this->extension->canViewField($resource, 'name'));
        $this->assertFalse($this->extension->canEditField($resource, 'diagnosis'));
        $this->assertTrue($this->extension->canViewField($resource, 'email'));
    }
}
