<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security;

use App\Security\FieldAccess;
use App\Security\SecuredResourceInterface;
use PHPUnit\Framework\TestCase;

class FieldAccessTest extends TestCase
{
    public function testConstructorWithValidResource(): void
    {
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('person');
        $resource->method('getPermissionId')->willReturn(1);
        
        $fieldName = 'email';
        
        $fieldAccess = new FieldAccess($resource, $fieldName);
        
        $this->assertSame($resource, $fieldAccess->resource);
        $this->assertSame($fieldName, $fieldAccess->field);
    }

    public function testGetResourceReturnsCorrectObject(): void
    {
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('test');
        $resource->method('getPermissionId')->willReturn(99);
        
        $fieldAccess = new FieldAccess($resource, 'test');
        
        $this->assertInstanceOf(SecuredResourceInterface::class, $fieldAccess->resource);
    }

    public function testGetFieldNameReturnsCorrectString(): void
    {
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('person');
        $resource->method('getPermissionId')->willReturn(10);
        
        $fieldName = 'password';
        $fieldAccess = new FieldAccess($resource, $fieldName);
        
        $this->assertEquals($fieldName, $fieldAccess->field);
    }

    public function testFieldAccessWithEmptyFieldName(): void
    {
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('test');
        $resource->method('getPermissionId')->willReturn(1);
        
        $fieldAccess = new FieldAccess($resource, '');
        
        $this->assertSame('', $fieldAccess->field);
    }

    public function testFieldAccessWithNumericFieldName(): void
    {
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('test');
        $resource->method('getPermissionId')->willReturn(5);
        
        $fieldName = '12345';
        $fieldAccess = new FieldAccess($resource, $fieldName);
        
        $this->assertSame($fieldName, $fieldAccess->field);
    }

    public function testFieldAccessWithSpecialCharactersInFieldName(): void
    {
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('test');
        $resource->method('getPermissionId')->willReturn(7);
        
        $fieldName = 'field_name_with-special.chars';
        $fieldAccess = new FieldAccess($resource, $fieldName);
        
        $this->assertSame($fieldName, $fieldAccess->field);
    }

    public function testFieldAccessIsReadonly(): void
    {
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('immutable');
        $resource->method('getPermissionId')->willReturn(1);
        
        $fieldName = 'immutable';
        $fieldAccess = new FieldAccess($resource, $fieldName);
        
        // Verificar que los valores son readonly
        $originalResource = $fieldAccess->resource;
        $originalFieldName = $fieldAccess->field;
        
        $this->assertSame($originalResource, $fieldAccess->resource);
        $this->assertSame($originalFieldName, $fieldAccess->field);
    }

    public function testMultipleFieldAccessInstancesAreIndependent(): void
    {
        $resource1 = $this->createMock(SecuredResourceInterface::class);
        $resource1->method('getPermissionDomain')->willReturn('domain1');
        $resource1->method('getPermissionId')->willReturn(1);
        
        $resource2 = $this->createMock(SecuredResourceInterface::class);
        $resource2->method('getPermissionDomain')->willReturn('domain2');
        $resource2->method('getPermissionId')->willReturn(2);
        
        $fieldAccess1 = new FieldAccess($resource1, 'field1');
        $fieldAccess2 = new FieldAccess($resource2, 'field2');
        
        $this->assertNotSame($fieldAccess1->resource, $fieldAccess2->resource);
        $this->assertNotEquals($fieldAccess1->field, $fieldAccess2->field);
    }

    public function testToStringReturnsCorrectFormat(): void
    {
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('person');
        $resource->method('getPermissionId')->willReturn(42);
        
        $fieldAccess = new FieldAccess($resource, 'email');
        
        $this->assertEquals('person:42.email', (string) $fieldAccess);
    }

    public function testToStringWithDifferentResourceTypes(): void
    {
        $resource = $this->createMock(SecuredResourceInterface::class);
        $resource->method('getPermissionDomain')->willReturn('patient');
        $resource->method('getPermissionId')->willReturn(100);
        
        $fieldAccess = new FieldAccess($resource, 'medical_history');
        
        $this->assertStringContainsString('patient', (string) $fieldAccess);
        $this->assertStringContainsString('100', (string) $fieldAccess);
        $this->assertStringContainsString('medical_history', (string) $fieldAccess);
    }
}
