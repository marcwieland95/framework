<?php

use Brain\Monkey\Functions;

class FieldTest extends TestCase
{
    /**
     * @test
     */
    public function on_dispatch_should_process_template_and_children()
    {
        $field = $this->getField();

        $field->getManager()->shouldReceive('dispatchTemplate')->once()->andReturn(null);

        $field->dispatch();
    }

    /**
     * @test
     */
    public function should_throw_exception_on_template_rendering()
    {
        $field = $this->getField();

        $this->expectException('Assely\Field\FieldException');

        $field->template(Mockery::mock('Illuminate\View\Factory'));
    }

    /**
     * @test
     */
    public function should_throw_on_setting_conditional_fields()
    {
        $field = $this->getField();

        $this->expectException('Assely\Field\FieldException');

        $field->on(true, []);
    }

    /**
     * @test
     */
    public function should_throw_on_setting_children_fields()
    {
        $field = $this->getField();

        $this->expectException('Assely\Field\FieldException');

        $field->children([]);
    }

    /**
     * @test
     */
    public function should_throw_on_setting_validator()
    {
        $field = $this->getField();

        $this->expectException('Assely\Field\FieldException');

        $field->validate(['required']);
    }

    /**
     * @test
     */
    public function should_throw_on_setting_sanitizer()
    {
        $field = $this->getField();

        $this->expectException('Assely\Field\FieldException');

        $field->sanitize(function () {
        });
    }

    /**
     * @test
     */
    public function should_throw_on_preview_rendering()
    {
        $field = $this->getField();

        $this->expectException('Assely\Field\FieldException');

        $field->preview(Mockery::mock('Illuminate\View\Factory'), []);
    }

    /**
     * @test
     */
    public function checking_field_type()
    {
        $field = $this->getField();

        $this->assertEquals(true, $field->isTypeOf('text'));
        $this->assertEquals(false, $field->isTypeOf('repeatable'));
    }

    /**
     * @test
     */
    public function by_default_fields_can_not_have_children()
    {
        $field = $this->getField();

        $this->assertEquals(false, $field->hasChildren());
    }

    /**
     * @test
     */
    public function by_default_fields_can_not_be_sanitized()
    {
        $field = $this->getField();

        $this->assertEquals(false, $field->needsSanitization());
    }

    /**
     * @test
     */
    public function can_be_serialized_to_json()
    {
        $field = $this->getField();

        $this->assertEquals(json_encode([
            'slug' => 'dummy_field_slug',
            'type' => 'text',
            'arguments' => $field->getArguments(),
            'singular' => 'Dummy Field Slug',
            'plural' => 'Dummy Field Slugs',
            'value' => null,
            'fingerprint' => $field->getFingerprint(),
            'children' => [],
            'validator' => $field->getManager()->getValidator(),
        ]), json_encode($field));
    }

    /**
     * @param array $arguments
     * @return mixed
     */
    protected function getField(array $arguments = [])
    {
        Functions::expect('sanitize_title')->once()->andReturn('dummy_field_slug');

        $field = $this->getMockForAbstractClass('Assely\Field\Field', [
            $this->getManagerMock(),
            $this->getChildrenFieldsMock(),
        ]);

        $field
            ->setType('text')
            ->setSlug('dummy_field_slug')
            ->setArguments($arguments)
            ->boot();

        return $field;
    }

    protected function getChildrenFieldsMock()
    {
        return Mockery::mock('Assely\Field\FieldsCollection');
    }

    /**
     * @return mixed
     */
    protected function getManagerMock()
    {
        $validator = Mockery::mock('Assely\Field\FieldValidator');

        $manager = Mockery::mock('Assely\Field\FieldManager', [
            Mockery::mock('Assely\Hook\HookFactory'),
            Mockery::mock('Assely\Asset\AssetFactory'),
            Mockery::mock('Illuminate\View\Factory'),
            $validator,
        ]);

        $manager->shouldReceive('boot')->once()->andReturn(null);
        $manager->shouldReceive('getValidator')->andReturn($validator);
        $validator->shouldReceive('jsonSerialize')->andReturn('{}');

        return $manager;
    }
}
