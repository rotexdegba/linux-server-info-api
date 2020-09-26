<?php
use Ginfo\Common;
use Ginfo\Info\Cpu;
use Ginfo\Info\Selinux;

class ExtendedLinux extends \Ginfo\OS\Linux {
    
    public function getCpu(): ?Cpu
    {
        $cpuInfo = Common::getContents('/proc/cpuinfo');
        if (null === $cpuInfo) {
            return null;
        }

        $cpuData = [];
        foreach (\explode("\n\n", $cpuInfo) as $block) {
            $cpuData[] = Common::parseKeyValueBlock($block);
        }
//print_r($cpuData);;
        $cores = (static function () use ($cpuData): int {
            $out = [];
            foreach ($cpuData as $block) {
                $out[$block['physical id']] = $block['cpu cores'];
            }

            return \array_sum($out);
        })();
        $virtual = \count($cpuData);
//print_r($cores);exit;
        return (new Cpu())
            ->setPhysical((static function () use ($cpuData): int {
                $out = [];
                foreach ($cpuData as $block) {
                    if (isset($out[$block['physical id']])) {
                        ++$out[$block['physical id']];
                    } else {
                        $out[$block['physical id']] = 1;
                    }
                }

                return \count($out);
            })())
            ->setVirtual($virtual)
            ->setCores($cores)
            ->setHyperThreading($cores < $virtual)
            ->setProcessors((static function () use ($cpuData): array {
                $out = [];
                foreach ($cpuData as $block) {
                    // overwrite data for physical processors
                    //$out[$block['physical id']] = (new Processor())
                    $out[$block['core id']] = (new \ExtendedCpuProcessor())
                        ->setAllProcessorInfo($block)
                        ->setModel($block['model name'])
                        ->setSpeed($block['cpu MHz'])
                        ->setL2Cache((float) $block['cache size'] * 1024) // L2 cache, drop KB
                        ->setFlags(\explode(' ', $block['flags']));

                    // todo: mips, arm
                    $out[$block['core id']]->setArchitecture('x86'); // default x86
                    foreach ($out[$block['core id']]->getFlags() as $flag) {
                        if ('lm' === $flag || '_lm' === \mb_substr($flag, -3)) { // lm, lahf_lm
                            $out[$block['core id']]->setArchitecture('x64');
                            break;
                        }
                        if ('ia64' === $flag) {
                            $out[$block['core id']]->setArchitecture('ia64');
                            break;
                        }
                    }
                }

                return $out;
            })());
    }
    
    /**
     * Had to override this method because the parent method which uses
     * Symfony\Component\Process\Process under the hood to run the sestatus
     * command was returning NULL on a system with selinux enabled. This version
     * uses shell_exec to run the sestatus command and it now works as expected.
     * 
     * @return Selinux|null
     */
    public function getSelinux(): ?\Ginfo\Info\Selinux {
        
        $sestatusCommandExists = shell_exec('command -v sestatus') !== NULL;
        $commandResult = '';

        if ($sestatusCommandExists) {
            
            $commandResult = shell_exec('sestatus');
            
            if($commandResult === NULL) {
            
                return null;
            }
        }

        $result = \trim($commandResult);
        $block = Common::parseKeyValueBlock($result);
        
        return (new Selinux())
            ->setEnabled(array_key_exists('SELinux status', $block) && ('enabled' === $block['SELinux status']))
            ->setPolicy((array_key_exists('Loaded policy name', $block) && $block['Loaded policy name']) ? $block['Loaded policy name'] : '')
            ->setMode((array_key_exists('Current mode', $block) && $block['Current mode']) ? $block['Current mode'] : '');
    }
}
