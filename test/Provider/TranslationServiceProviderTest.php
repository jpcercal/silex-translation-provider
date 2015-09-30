<?php

namespace Cekurte\Silex\Translation\Test\Provider;

use Cekurte\Silex\Translation\Provider\TranslationServiceProvider;
use Cekurte\Tdd\ReflectionTestCase;
use Silex\Application;
use Silex\Provider\TranslationServiceProvider as SilexTranslationServiceProvider;

class TranslationServiceProviderTest extends ReflectionTestCase
{
    public function testImplementsServiceProviderInterface()
    {
        $reflection = new \ReflectionClass(
            '\\Cekurte\\Silex\\Translation\\Provider\\TranslationServiceProvider'
        );

        $this->assertTrue($reflection->implementsInterface(
            '\\Silex\\ServiceProviderInterface'
        ));
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage The TranslationServiceProvider is not registered in this application
     */
    public function testBootAppTranslatorNotIsset()
    {
        $app = new Application();

        $app->register(new TranslationServiceProvider());

        $app->boot();
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage The translation directory parameter is not registered in this application
     */
    public function testBootAppTranslationDirectoryNotIsset()
    {
        $app = new Application();

        $app->register(new SilexTranslationServiceProvider());
        $app->register(new TranslationServiceProvider());

        $app->boot();
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage The translation directory not exists
     */
    public function testRegisterParameter()
    {
        $app = new Application();

        $transDirectory = realpath(__DIR__ . '/directory-not-exists/');

        $app->register(new SilexTranslationServiceProvider());
        $app->register(new TranslationServiceProvider(), [
            'translation.directory' => $transDirectory
        ]);

        $this->assertEquals($transDirectory, $app['translation.directory']);

        $app->boot();
    }

    public function testBoot()
    {
        $app = new Application();

        $transDirectory = realpath(__DIR__ . '/../Resources/i18n/');

        $provider = $this
            ->getMockBuilder('\\Cekurte\\Silex\\Translation\\Provider\\TranslationServiceProvider')
            ->setMethods(['fileIsAllowed'])
            ->getMock()
        ;

        $provider
            ->expects($this->once())
            ->method('fileIsAllowed')
            ->withAnyParameters()
            ->will($this->returnValue(true))
        ;

        $app->register(new SilexTranslationServiceProvider());
        $app->register($provider, [
            'translation.directory' => $transDirectory
        ]);

        $app->boot();

        $this->assertInstanceOf(
            '\\Silex\\Translator',
            $app['translator']
        );

        $loaders = $this->invokeMethod($app['translator'], 'getLoaders');

        $this->assertInstanceOf(
            '\\Symfony\\Component\\Translation\\Loader\\YamlFileLoader',
            $loaders['yaml']
        );

        $this->assertEquals('Hello', $app['translator']->trans('hello'));
    }

    public function dataProviderGetLocale()
    {
        return [
            ['en.yml',  'yml',  'en'],
            ['fr.yml',  'yml',  'fr'],
            ['es.yml',  'yml',  'es'],
            ['en.yaml', 'yaml', 'en'],
            ['fr.yaml', 'yaml', 'fr'],
            ['es.yaml', 'yaml', 'es'],
            ['en.php',  'php',  'en'],
            ['fr.php',  'php',  'fr'],
            ['es.php',  'php',  'es'],
        ];
    }

    /**
     * @dataProvider dataProviderGetLocale
     */
    public function testGetLocale($filename, $extension, $locale)
    {
        $directoryIterator = $this
            ->getMockBuilder('DirectoryIterator')
            ->setMethods(['getFilename', 'getExtension'])
            ->enableOriginalConstructor()
            ->setConstructorArgs([__DIR__])
            ->getMock()
        ;

        $directoryIterator
            ->expects($this->once())
            ->method('getFilename')
            ->will($this->returnValue($filename))
        ;

        $directoryIterator
            ->expects($this->once())
            ->method('getExtension')
            ->will($this->returnValue($extension))
        ;

        $provider = new TranslationServiceProvider();

        $this->assertEquals(
            $locale,
            $this->invokeMethod($provider, 'getLocale', [$directoryIterator])
        );
    }

    public function dataProviderFileIsAllowed()
    {
        return [
            [false,  'dir',  false],
            [true,   'yml',  true],
            [true,   'yaml', true],
            [true,   'php',  false],
            [true,   'ini',  false],
        ];
    }

    /**
     * @dataProvider dataProviderFileIsAllowed
     */
    public function testFileIsAllowed($isFile, $extension, $fileIsAllowed)
    {
        $directoryIterator = $this
            ->getMockBuilder('DirectoryIterator')
            ->setMethods(['isFile', 'getExtension'])
            ->enableOriginalConstructor()
            ->setConstructorArgs([__DIR__])
            ->getMock()
        ;

        $directoryIterator
            ->expects($this->once())
            ->method('isFile')
            ->will($this->returnValue($isFile))
        ;

        if ($extension !== 'dir') {
            $directoryIterator
                ->expects($this->once())
                ->method('getExtension')
                ->will($this->returnValue($extension))
            ;
        }

        $provider = new TranslationServiceProvider();

        $this->assertEquals(
            $fileIsAllowed,
            $this->invokeMethod($provider, 'fileIsAllowed', [$directoryIterator])
        );
    }
}
