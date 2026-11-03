import DemoCard from '@/components/demo/demo-card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { router } from '@inertiajs/react';
import { useState } from 'react';

interface StorageCardProps {
    configured: boolean;
    files: string[];
}

export default function StorageCard({ configured, files }: StorageCardProps) {
    const [file, setFile] = useState<File | null>(null);
    const [error, setError] = useState<string | null>(null);
    const [sending, setSending] = useState(false);

    const upload = () => {
        if (!file) return;
        router.post(
            '/demo/files',
            { file },
            {
                forceFormData: true,
                preserveScroll: true,
                onStart: () => {
                    setSending(true);
                    setError(null);
                },
                onError: (errors) => setError(errors.file ?? 'The upload failed.'),
                onSuccess: () => setFile(null),
                onFinish: () => setSending(false),
            },
        );
    };

    return (
        <DemoCard title="Storage" description="Uploads a small file (up to 1 MB) to the shared disk and lists what is there." configured={configured}>
            <div className="flex gap-2">
                <Input type="file" disabled={!configured} onChange={(e) => setFile(e.target.files?.[0] ?? null)} />
                <Button onClick={upload} disabled={!configured || !file || sending}>
                    Upload
                </Button>
            </div>
            {error && <p className="text-destructive">{error}</p>}
            {configured && (
                <ul className="text-muted-foreground list-inside list-disc">
                    {files.length === 0 ? <li className="list-none">No files yet.</li> : files.map((path) => <li key={path}>{path}</li>)}
                </ul>
            )}
        </DemoCard>
    );
}
