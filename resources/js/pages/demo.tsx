import CacheCard from '@/components/demo/cache-card';
import DatabaseCard from '@/components/demo/database-card';
import EnvironmentCard from '@/components/demo/environment-card';
import LogsCard from '@/components/demo/logs-card';
import QueueCard from '@/components/demo/queue-card';
import StorageCard from '@/components/demo/storage-card';
import { type DemoFeatures } from '@/types';
import { Head } from '@inertiajs/react';

interface DemoProps {
    features: DemoFeatures;
    queue_result: string | null;
    files: string[];
}

export default function Demo({ features, queue_result, files }: DemoProps) {
    return (
        <>
            <Head title="Arco starter" />
            <main className="mx-auto flex min-h-screen w-full max-w-5xl flex-col gap-6 p-6 lg:p-10">
                <header className="space-y-1">
                    <h1 className="text-2xl font-semibold">Arco starter</h1>
                    <p className="text-muted-foreground">
                        Each card exercises one Arco Cloud feature. Add the missing ones in Arco Cloud → Resources.
                    </p>
                </header>
                <div className="grid gap-4 md:grid-cols-2">
                    <QueueCard configured={features.queue} sharedStore={features.cache || features.storage} initialResult={queue_result} />
                    <StorageCard configured={features.storage} files={files} />
                    <CacheCard configured={features.cache} />
                    <LogsCard />
                    <DatabaseCard configured={features.database} />
                    <EnvironmentCard environment={features.environment} />
                </div>
            </main>
        </>
    );
}
