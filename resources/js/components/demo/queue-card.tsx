import DemoCard from '@/components/demo/demo-card';
import { Button } from '@/components/ui/button';
import { demoRequest } from '@/lib/demo-api';
import { useEffect, useRef, useState } from 'react';

interface QueueCardProps {
    configured: boolean;
    sharedStore: boolean;
    initialResult: string | null;
}

const POLL_EVERY_MS = 2000;
const POLL_FOR_MS = 60000;

export default function QueueCard({ configured, sharedStore, initialResult }: QueueCardProps) {
    const [finishedAt, setFinishedAt] = useState<string | null>(initialResult);
    const [status, setStatus] = useState<'idle' | 'waiting' | 'timeout' | 'error'>('idle');
    const timer = useRef<ReturnType<typeof setInterval> | null>(null);

    const stop = () => {
        if (timer.current) {
            clearInterval(timer.current);
            timer.current = null;
        }
    };

    useEffect(() => stop, []);

    const run = async () => {
        stop();
        setStatus('waiting');
        const before = finishedAt;

        const response = await demoRequest('/demo/job');
        if (!response.ok) {
            setStatus('error');
            return;
        }
        if (!sharedStore) {
            setStatus('idle');
            return;
        }

        const startedAt = Date.now();
        timer.current = setInterval(async () => {
            const poll = await demoRequest('/demo/job', 'GET')
                .then((r) => r.json())
                .catch(() => null);
            if (poll?.finished_at && poll.finished_at !== before) {
                stop();
                setFinishedAt(poll.finished_at);
                setStatus('idle');
            } else if (Date.now() - startedAt > POLL_FOR_MS) {
                stop();
                setStatus('timeout');
            }
        }, POLL_EVERY_MS);
    };

    return (
        <DemoCard title="Queue" description="Sends a job to the queue; a worker runs it and writes a log line." configured={configured}>
            <Button onClick={run} disabled={!configured || status === 'waiting'}>
                {status === 'waiting' ? 'Waiting for the job…' : 'Run a job'}
            </Button>
            {finishedAt && <p>Last job finished at {new Date(finishedAt).toLocaleString()}.</p>}
            {configured && !sharedStore && status === 'idle' && (
                <p className="text-muted-foreground">The job ran. The worker and the web function share no storage, so look for its line in Logs.</p>
            )}
            {status === 'timeout' && <p className="text-muted-foreground">No result after 60 seconds. Check Logs in Arco Cloud.</p>}
            {status === 'error' && <p className="text-destructive">The job could not be sent.</p>}
        </DemoCard>
    );
}
