import DemoCard from '@/components/demo/demo-card';
import { Button } from '@/components/ui/button';
import { demoRequest } from '@/lib/demo-api';
import { useState } from 'react';

export default function LogsCard() {
    const [message, setMessage] = useState<string | null>(null);

    const send = async (path: string, done: string) => {
        await demoRequest(path);
        setMessage(done);
    };

    return (
        <DemoCard title="Logs" description="Writes a log line, or throws a test exception, so you can see them in Arco Cloud." configured>
            <div className="flex flex-wrap gap-2">
                <Button onClick={() => send('/demo/log', 'Log line written.')}>Write a log line</Button>
                <Button variant="outline" onClick={() => send('/demo/error', 'Test exception thrown.')}>
                    Throw a test exception
                </Button>
            </div>
            {message && <p>{message}</p>}
            <p className="text-muted-foreground">Open Logs in Arco Cloud to see it.</p>
        </DemoCard>
    );
}
